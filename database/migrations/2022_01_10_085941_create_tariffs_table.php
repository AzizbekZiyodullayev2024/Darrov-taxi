<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tariffs', function(Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('client');
            $table->integer('minute');
            $table->integer('km');
            $table->bigInteger('vip');
            $table->integer('min_pay');
            $table->integer('out_city');
            $table->integer('min_km');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('tariffs');
    }
}
