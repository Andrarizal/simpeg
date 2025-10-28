<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // DB::statement("CREATE TYPE sex AS ENUM ('L', 'P')");
        // DB::statement("CREATE TYPE last_education AS ENUM ('SMA', 'D3', 'D4/S1', 'S2', 'S3')");

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nik')->unique();
            $table->string('name');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->enum('sex', ['L', 'P'])->default('L');
            $table->text('address');
            $table->string('phone')->unique();
            $table->string('personal_email')->unique();
            $table->string('office_email')->unique();
            $table->enum('last_education', ['SMA', 'D3', 'D4/S1', 'S2', 'S3'])->default('SMA');
            $table->date('work_entry_date');
            $table->foreignId('unit_id')->constrained(
                table: 'units',
                indexName: 'staff_unit_id'
            );
            $table->foreignId('chair_id')->constrained(
                table: 'chairs',
                indexName: 'staff_chair_id'
            );
            $table->timestamps();
        });

        Schema::table('units', function (Blueprint $table) {
            $table->foreign('leader_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
        // DB::statement('DROP TYPE IF EXISTS sex');
        // DB::statement('DROP TYPE IF EXISTS last_education');
    }
};
