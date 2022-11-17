<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            /*
             * Поячснение!!!
             * Инструкция $table->foreignId('vendor_id')->constrained(); для создания нешнего ключа
             * Эквивалентна следующим инструциям:
             * $table->unsignedBigInteger('vendor_id');
             * $table->foreign('vendor_id')->->references('id')->on('vendors')
             */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('models');
    }
};
