<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = ['pas', 'nik', 'nip', 'name', 'birth_place', 'birth_date', 'sex', 'marital', 'origin', 'domicile', 'email', 'phone', 'other_phone', 'other_phone_adverb', 'entry_date', 'retirement_date', 'staff_status_id', 'chair_id', 'group_id', 'unit_id'];

    protected static function booted()
    {
        // EVENT: SAAT DIBUAT (CREATED)
        static::created(function ($staff) {
            StaffAdministration::create([
                'staff_id' => $staff->id,
            ]);
        });

        static::saved(function ($staff) {
            if ($staff->is_processing_history) return; 

            DB::transaction(function () use ($staff) {
                $staff->is_processing_history = true; 

                // SKENARIO 1: PEGAWAI BARU
                if ($staff->wasRecentlyCreated) {
                    $staff->createHistoryBasedOnStatus();
                }

                // SKENARIO 2: STATUS BERUBAH (Misal: Kontrak -> Tetap)
                elseif ($staff->wasChanged('staff_status_id')) {
                    // Paksa tutup yang lama
                    $staff->closePreviousHistories(); 
                    $staff->createHistoryBasedOnStatus();
                }

                // SKENARIO 3: STATUS TETAP, DATA RELASI BERUBAH
                else {
                    $created = false; // Flag penanda

                    if ($staff->staff_status_id == 1) { // Tetap
                        $adjustment = $staff->adjustment()->latest()->first();
                        $appointment = $staff->appointment()->latest()->first();

                        // Cek Adjustment
                        if ($adjustment && $adjustment->updated_at->diffInSeconds(now()) < 5) {
                            // Coba buat history adjustment
                            $created = $staff->createWorkHistoryFromModel($adjustment, 'adjustment');
                        
                            if ($created) {
                                $staff->closePreviousHistories(); 
                            }
                        }
                        
                        // Cek Appointment (Hanya jika adjustment tidak tereksekusi/gagal)
                        if (!$created && $appointment && $appointment->updated_at->diffInSeconds(now()) < 5) {
                            $created = $staff->createWorkHistoryFromModel($appointment, 'appointment');
                        
                            if ($created) {
                                $staff->closePreviousHistories();
                            }
                        }
                    }
                    
                    elseif ($staff->staff_status_id == 2) { // Kontrak
                        $contract = $staff->contract()->latest()->first();
                        
                        if ($contract && $contract->updated_at->diffInSeconds(now()) < 5) {
                            $created = $staff->createWorkHistoryFromModel($contract, 'contract');
                        
                            if ($created) {
                                $staff->closePreviousHistories();
                            }
                        }
                    }
                }

                $staff->is_processing_history = false;
            });
        });
    }

    public function closePreviousHistories()
    {
        $latest = $this->workHistories()->latest('id')->first();
        
        if (!$latest) return;

        $this->workHistories()
            ->whereNull('end_date')
            ->where('id', '!=', $latest->id) // Jangan tutup diri sendiri
            ->update([
                'end_date' => $latest->start_date->subDay()
            ]);
    }

    public function createHistoryBasedOnStatus()
    {
        if ($this->staff_status_id == 2) {
            $data = $this->contract()->latest()->first();
            if ($data) $this->createWorkHistoryFromModel($data, 'contract');
        }
        elseif ($this->staff_status_id == 1) {
            $data = $this->appointment()->latest()->first();
            if ($data) $this->createWorkHistoryFromModel($data, 'appointment');
        }
    }

    public function createWorkHistoryFromModel($sourceModel, $type)
    {
        // 1. Ekstrak Data Calon History Baru
        $decreeNumber = match($type) {
            'contract' => $sourceModel->contract_number,
            'appointment' => $sourceModel->decree_number,
            'adjustment' => $sourceModel->decree_number,
            default => null
        };

        $decreeDate = match($type) {
            'contract' => $sourceModel->start_date, 
            'appointment' => $sourceModel->decree_date,
            'adjustment' => $sourceModel->decree_date,
            default => now()
        };

        $decreeFile = $sourceModel->decree ?? null; 
        $class = $sourceModel->class ?? null; 

        // 2. CEK HISTORY TERAKHIR (ANTI DUPLIKAT)
        // Ambil history paling terakhir yang dibuat
        $lastHistory = $this->workHistories()->latest('id')->first();

        // Logika Pengecekan:
        // Jika history terakhir punya Nomor SK, Status, dan Golongan yang SAMA
        // Maka anggap ini duplikat trigger event, JANGAN BUAT LAGI.
        if ($lastHistory && 
            $lastHistory->staff_status_id == $this->staff_status_id &&
            $lastHistory->decree_number == $decreeNumber &&
            $lastHistory->class == $class
        ) {
            // Abaikan, karena data tidak berubah
            return false; 
        }

        // 3. JIKA BEDA, BARU BUAT
        WorkHistory::create([
            'staff_id' => $this->id,
            'unit_id' => $this->unit_id,
            'chair_id' => $this->chair_id,
            'staff_status_id' => $this->staff_status_id,
            'start_date' => $decreeDate ?? now(),
            'decree_number' => $decreeNumber,
            'decree_date' => $decreeDate,
            'decree' => $decreeFile,
            'class' => $class,
        ]);

        return true; // Berhasil buat baru
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function staffStatus(): BelongsTo {
        return $this->belongsTo(StaffStatus::class);
    }

    public function chair(): BelongsTo {
        return $this->belongsTo(Chair::class);
    }

    public function group(): BelongsTo {
        return $this->belongsTo(Group::class);
    }

    public function unit(): BelongsTo {
        return $this->belongsTo(Unit::class);
    }

    public function contract(): HasOne { 
        return $this->hasOne(StaffContract::class); 
    }

    public function appointment(): HasOne { 
        return $this->hasOne(StaffAppointment::class); 
    }
    
    public function adjustment(): HasOne { 
        return $this->hasOne(StaffAdjustment::class); 
    }

    public function entryEducation(): HasOne {
        return $this->hasOne(StaffEntryEducation::class); 
    }

    public function workEducation(): HasOne {
        return $this->hasOne(StaffWorkEducation::class); 
    }

    public function workExperience(): HasOne {
        return $this->hasOne(StaffWorkExperience::class); 
    }

    public function administration(): HasOne
    {   
        return $this->hasOne(StaffAdministration::class);
    }

    public function overtimes(): HasMany
    {
        return $this->hasMany(Overtime::class);
    }

    public function training(): HasMany {
        return $this->hasMany(StaffTraining::class);
    }

    public function schedule(): HasMany {
        return $this->hasMany(Schedule::class);
    }

    public function getTrainingHoursYearAttribute() {
        return $this->trainings()
            ->whereYear('training_date', now()->year)
            ->sum('duration');
    }

    public function workHistories(): HasMany
    {
        return $this->hasMany(WorkHistory::class);
    }

    public function currentWork(): HasOne
    {
        return $this->hasOne(WorkHistory::class)->whereNull('end_date')->latestOfMany();
    }
}
