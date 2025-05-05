<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('drivers', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->unique();
            $table->string('password')->nullable();
            $table->date('birth_date')->nullable();
            $table->tinyInteger('gender')->default(1);
            $table->string('card_number')->nullable();
            $table->string('gmail')->nullable();
            $table->string('license')->nullable();
            $table->string('img')->nullable();
            $table->bigInteger('sum')->default(0);
            $table->tinyInteger('vip')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->float('longitude', 9, 6)->default(0);
            $table->float('latitude', 9, 6)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('drivers');
    }
}
