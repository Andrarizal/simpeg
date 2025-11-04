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
        Schema::create('staff_entry_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained(
                table: 'staff',
                indexName: 'staff_entry_education_staff_id'
            )->onDelete('cascade');
            $table->enum('level', ['Dokter', 'Dokter Gigi','Spesialis', 'S2', 'S1', 'Profesi Ners', 'Profesi Apoteker', 'DIV', 'DIII', 'DIII Anestesi', 'DIV Anestesi', 'SMK', 'SMA', 'SMP'
            ]);
            $table->string('institution');
            $table->string('certificate_number');
            $table->date('certificate_date');
            $table->date('certificate')->nullable();
            $table->string('nonformal_education')->nullable();
            $table->string('adverb')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_entry_educations');
    }
};
