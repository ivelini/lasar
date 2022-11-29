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
        Schema::create('api_url_sallers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saller_id')->constrained('sallers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('label_id')->constrained('label_import_catalog_service')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('url');
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
        Schema::dropIfExists('api_url_sallers');
    }
};
