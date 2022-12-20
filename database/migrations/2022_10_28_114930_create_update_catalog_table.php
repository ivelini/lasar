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
        Schema::create('update_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->boolean('is_job_created')->nullable()->default(null);
            $table->boolean('is_catalog_updated')->nullable()->default(null);
            $table->longText('error')->nullable()->default(null);
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
        Schema::dropIfExists('update_catalog');
    }
};
