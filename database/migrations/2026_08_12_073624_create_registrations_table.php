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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 32)->unique();
            $table->string('name', 120);
            $table->string('email');
            $table->string('phone', 30);
            $table->string('gender', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('school', 150)->nullable();
            $table->text('address')->nullable();
            $table->foreignId('division_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->text('reason');
            $table->text('organization_experience')->nullable();
            $table->string('instagram', 100)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['division_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
