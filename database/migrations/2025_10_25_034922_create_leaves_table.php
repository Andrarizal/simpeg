<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Cuti', 'Izin']);
            $table->enum('subtype', ['Tahunan', 'Melahirkan', 'Duka', 'Menikah', 'Ibadah Haji', 'Khitan Anak', 'Baptis Anak', 'Non-Sakit', 'Sakit']);
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'leaves_staff_id'
            )->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->unsignedTinyInteger('remaining')->nullable();
            $table->string('evidence')->nullable();
            $table->unsignedTinyInteger('is_replaced')->nullable()->default(null);
            $table->foreignId('replacement_id')->constrained(
                table: 'staff',
                indexName: 'leaves_replacement_id'
            )->cascadeOnDelete();
            $table->date('replacement_at')->nullable();
            $table->enum('status', ['Menunggu', 'Diketahui Kepala Unit', 'Diketahui Koordinator', 'Disetujui Kepala Seksi', 'Disetujui Direktur', 'Ditolak']);
            $table->foreignId('known_by')->nullable()->constrained(
                table: 'staff',
                indexName: 'leaves_known_by'
            )->nullOnDelete();
            $table->date('known_at')->nullable();
            $table->foreignId('approver_id')->nullable()->constrained(
                table: 'staff',
                indexName: 'leaves_approver_id'
            )->nullOnDelete();
            $table->date('approve_at')->nullable();
            $table->unsignedTinyInteger('is_verified')->nullable()->default(null);
            $table->foreignId('verified_by')->nullable()->constrained(
                table: 'staff',
                indexName: 'overtimes_verified_by'
            )->nullOnDelete();
            $table->date('verified_at')->nullable();
            $table->string('adverb')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
