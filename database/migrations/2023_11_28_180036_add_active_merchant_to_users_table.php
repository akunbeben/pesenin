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
            $table->after('require_reset', function (Blueprint $table) {
                $table->unsignedBigInteger('active_merchant')->nullable();
                $table->foreign('active_merchant')->references('id')->on('merchants');
                $table->unsignedBigInteger('employee_of')->nullable();
                $table->foreign('employee_of')->references('id')->on('merchants');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_active_merchant_foreign');
            $table->dropColumn(['active_merchant']);
            $table->dropForeign('users_employee_of_foreign');
            $table->dropColumn(['employee_of']);
        });
    }
};
