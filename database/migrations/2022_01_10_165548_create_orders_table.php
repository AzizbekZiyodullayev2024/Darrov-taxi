<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('driver_id')->default(0);
            $table->integer('total_price');
            $table->integer('item_count');
            $table->string('address')->nullable();
            $table->float('longitude', 9, 6)->default(0);
            $table->float('latitude', 9, 6)->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('payment_type')->default(1);
            $table->integer('delivery_price')->default(0);
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
        Schema::dropIfExists('orders');
    }
}
