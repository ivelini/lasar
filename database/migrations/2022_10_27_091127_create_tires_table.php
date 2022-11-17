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
        Schema::create('tires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('models')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('num');
            $table->integer('width');
            $table->integer('height');
            $table->integer('diameter');
            $table->boolean('is_spikes')->nullable()->default(0);
            $table->string('index_speed')->nullable()->default(null);
            $table->string('index_load')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tires');
    }
};
