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
        Schema::create('screening_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('question_text');
            $table->unsignedSmallInteger('sort_order')->default(1)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['division_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screening_questions');
    }
};
