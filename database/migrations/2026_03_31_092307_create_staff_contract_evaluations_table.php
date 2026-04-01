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
        Schema::create('staff_contract_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained(
                table: 'staff_contracts',
                indexName: 'staff_contract_evaluations_contract_id',
            )->cascadeOnDelete();
            $table->foreignId('first_score_id')->nullable()->constrained(
                table: 'performance_appraisals',
                indexName: 'staff_contract_evaluations_first_score_id',
            )->nullOnDelete();
            $table->foreignId('second_score_id')->nullable()->constrained(
                table: 'performance_appraisals',
                indexName: 'staff_contract_evaluations_second_score_id',
            )->nullOnDelete();
            $table->integer('final_score')->nullable();
            $table->enum('conclusion', ['Lulus', 'Tidak Lulus'])->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_contract_evaluations');
    }
};
