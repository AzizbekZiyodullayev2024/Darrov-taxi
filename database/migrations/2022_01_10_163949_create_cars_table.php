<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Driver;

class CreateCarsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cars', function(Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('color');
            $table->tinyInteger('count_seats');
            $table->year('manufacture_date');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('tariff_id');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();


            $table->foreign('driver_id')
                ->references('id')
                ->on(Driver::table())
                ->onDelete('CASCADE');

            $table->foreign('type_id')
                ->references('id')
                ->on('car_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('cars');
    }
}
