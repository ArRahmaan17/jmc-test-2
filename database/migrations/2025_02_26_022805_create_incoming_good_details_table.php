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
        Schema::create('incoming_good_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('incomingId')->unsigned();
            $table->foreign('incomingId')->references('id')->on('incoming_goods')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('price');
            $table->integer('amount');
            $table->string('unit');
            $table->string('total');
            $table->timestamp('expired_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_good_details');
    }
};
