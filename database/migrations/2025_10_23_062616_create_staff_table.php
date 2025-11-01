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
            $table->string('nip')->unique();
            $table->string('name');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->enum('sex', ['L', 'P']);
            $table->enum('marital', ['Lajang', 'Menikah', 'Cerai Hidup', 'Cerai Mati']);
            $table->text('address');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('other_phone')->unique();
            $table->enum('other_phone_adverb', ['Suami', 'Istri', 'Orang tua', 'Wali', 'Saudara', 'Lainnya']);
            $table->date('entry_date');
            $table->date('retirement_date');
            $table->foreignId('staff_status_id')->constrained(
                table: 'staff_statuses',
                indexName: 'staff_staff_status_id'
            )->cascadeOnDelete();
            $table->foreignId('chair_id')->constrained(
                table: 'chairs',
                indexName: 'staff_chair_id'
            )->cascadeOnDelete();
            $table->foreignId('group_id')->constrained(
                table: 'groups',
                indexName: 'staff_group_id'
            )->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained(
                table: 'units',
                indexName: 'staff_unit_id'
            )->cascadeOnDelete();
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
