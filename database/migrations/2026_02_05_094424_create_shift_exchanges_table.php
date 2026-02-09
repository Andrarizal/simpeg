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
        Schema::create('shift_exchanges', function (Blueprint $table) {
            $table->id();
            $table->date('exchange_date');
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'shift_exchanges_staff_id'
            )->cascadeOnDelete();
            $table->foreignId('replacer_id')->constrained(
                table: 'staff',
                indexName: 'shift_exchanges_replacer_id'
            )->cascadeOnDelete();
            $table->foreignId('staff_schedule_id')->constrained(
                table: 'schedules',
                indexName: 'shift_exchanges_staff_schedule_id'
            )->cascadeOnDelete();
            $table->foreignId('replacer_schedule_id')->constrained(
                table: 'schedules',
                indexName: 'shift_exchanges_replacer_schedule_id'
            )->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->foreignId('approved_by')->nullable()->constrained(
                table: 'staff',
                indexName: 'shift_exchanges_approved_by'
            )->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_exchanges');
    }
};
