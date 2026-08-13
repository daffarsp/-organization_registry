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
        Schema::create('registration_screening_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('screening_question_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('answer');
            $table->timestamps();

            $table->unique(['registration_id', 'screening_question_id'], 'registration_screening_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_screening_answers');
    }
};
