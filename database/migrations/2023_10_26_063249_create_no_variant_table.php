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
        Schema::create('no_variant', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id')->unsigned();
            $table->text('serial_no')->nullable();
            $table->text('barcode')->nullable();
            $table->date('expiration')->nullable();
            $table->double('quantity')->default(0);
            $table->double('unit_cost')->default(0);
            $table->double('selling_price')->default(0);
            $table->integer('item_status_id')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('no_variant');
    }
};
