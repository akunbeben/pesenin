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
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('external_id')->nullable()->after('id');
            $table->foreignUuid('category_external_id')
                ->nullable()
                ->after('category_id')
                ->references('external_id')
                ->on('categories')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('category_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_category_external_id_foreign');
            $table->dropColumn(['external_id', 'category_external_id']);
            $table->unsignedBigInteger('category_id')->change();
        });
    }
};
