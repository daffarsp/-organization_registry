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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('is_admin')->index();
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->integer('score')->nullable()->after('status')->index();
            $table->timestamp('test_completed_at')->nullable()->after('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['score', 'test_completed_at']);
        });
    }
};
