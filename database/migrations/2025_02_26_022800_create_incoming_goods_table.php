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
        Schema::create('incoming_goods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('operatorId')->unsigned();
            $table->foreign('operatorId')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('categoryId')->unsigned();
            $table->foreign('categoryId')
                ->references('id')
                ->on('categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('subCategoryId')->unsigned();
            $table->foreign('subCategoryId')
                ->references('id')
                ->on('sub_categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('source');
            $table->string('unit')->default('Gudang 1');
            $table->boolean('status')->default(false);
            $table->string('mail_number')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_goods');
    }
};
