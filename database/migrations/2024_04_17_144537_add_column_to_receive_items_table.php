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
        Schema::table('receive_items', function (Blueprint $table) {
            $table->double('shipping_cost')->default(0)->after('unit_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receive_items', function (Blueprint $table) {
            //
        });
    }
};
