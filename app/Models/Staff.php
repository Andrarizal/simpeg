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
        static::created(function ($staff) {
            StaffAdministration::create([
                'staff_id' => $staff->id,
            ]);
            $staff->createHistoryAuto();
        });

        static::saved(function ($staff) {
            if ($staff->is_processing_history) return;

            $statusChanged = $staff->wasChanged(['staff_status_id', 'unit_id', 'chair_id', 'group_id']);

            DB::transaction(function () use ($staff, $statusChanged) {
                $staff->is_processing_history = true;

                if ($statusChanged) {
                    $staff->closePreviousHistories();
                    $staff->createHistoryAuto();
                } 
                else {
                    $historyCreated = $staff->createHistoryIfNewDocumentFound();

                    if ($historyCreated) {
                        $staff->closePreviousHistories();
                    }
                }

                $staff->is_processing_history = false;
            });
        });

        static::deleting(function (Staff $staff) {
            User::where('staff_id', $staff->id)->delete();
        });
    }

    public function createHistoryAuto()
    {
        if ($this->staff_status_id == 1) {
            $adj = $this->adjustment()->latest()->first();
            if ($adj && $this->isNewDocument($adj, 'adjustment')) {
                return $this->createWorkHistoryFromModel($adj, 'adjustment');
            }

            $app = $this->appointment()->latest()->first();
            if ($app) {
                return $this->createWorkHistoryFromModel($app, 'appointment');
            }
        }
        
        elseif ($this->staff_status_id == 2) {
            $con = $this->contract()->latest()->first();
            if ($con) {
                return $this->createWorkHistoryFromModel($con, 'contract');
            }
        }

        else {
            return WorkHistory::create([
                'staff_id' => $this->id,
                'unit_id' => $this->unit_id,
                'chair_id' => $this->chair_id,
                'staff_status_id' => $this->staff_status_id,
                'start_date' => now(),
            ]);
        }

        return null;
    }

    public function createHistoryIfNewDocumentFound()
    {
        if ($this->staff_status_id == 1) {
            $adj = $this->adjustment()->latest()->first();
            
            if ($adj && $this->isNewDocument($adj, 'adjustment')) {
                return $this->createWorkHistoryFromModel($adj, 'adjustment');
            }
        }
        
        if ($this->staff_status_id == 2) {
            $con = $this->contract()->latest()->first();
            if ($con && $this->isNewDocument($con, 'contract')) {
                return $this->createWorkHistoryFromModel($con, 'contract');
            }
        }

        return false;
    }

    public function isNewDocument($sourceModel, $type)
    {
        $newDecreeNumber = match($type) {
            'contract' => $sourceModel->contract_number,
            'appointment' => $sourceModel->decree_number,
            'adjustment' => $sourceModel->decree_number,
            default => null
        };

        $lastHistory = $this->workHistories()->latest('id')->first();

        if (!$lastHistory) return true;

        if ($lastHistory->decree_number === $newDecreeNumber) {
            return false; 
        }

        return true;
    }

    public function createWorkHistoryFromModel($sourceModel, $type)
    {
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

        return true;
    }

    public function closePreviousHistories()
    {
        $latest = $this->workHistories()->latest('id')->first();
        
        if (!$latest) return;

        $this->workHistories()
            ->whereNull('end_date')
            ->where('id', '!=', $latest->id)
            ->update([
                'end_date' => $latest->start_date->subDay()
            ]);
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

    public function leave(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function replacer(): HasMany
    {
        return $this->hasMany(Leave::class, 'replacement_id');
    }

    public function performance(): HasMany
    {
        return $this->hasMany(StaffPerformance::class);
    }

    public function overtime(): HasMany
    {
        return $this->hasMany(Overtime::class);
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }
}
