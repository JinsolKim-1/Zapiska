<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add only if it doesn't exist
            if (!Schema::hasColumn('orders', 'company_id')) {
                $table->unsignedBigInteger('company_id')->after('created_by');

                $table->foreign('company_id')
                      ->references('company_id')
                      ->on('companies')
                      ->onDelete('cascade')
                      ->onUpdate('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};
