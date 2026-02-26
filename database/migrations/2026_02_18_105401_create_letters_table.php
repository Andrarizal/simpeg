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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->enum('classification', ['Disposisi', 'Undangan']);
            $table->string('agenda_number')->nullable();
            $table->string('reference_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('letter_date');
            $table->foreignId('template_id')->nullable()->constrained(
                table: 'letter_templates',
                indexName: 'letters_template_id'
            )->nullOnDelete();
            $table->enum('receiver_type', ['Terlampir', 'Utuh'])->nullable();
            $table->string('urgency')->nullable();
            $table->string('sender')->nullable();
            $table->string('title');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->text('instruction')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('known_by')->nullable()->constrained(
                table: 'staff',
                indexName: 'letters_known_by'
            )->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
