<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVesselsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vessel_type_id');
            $table->unsignedBigInteger('owner_id');
            $table->string('name');
            $table->integer('seat_capacity');
            $table->double('max_accompanied_cargo');
            $table->double('max_unaccompanied_cargo');
            $table->timestamps();
            $table->foreign('vessel_type_id')->references('id')->on('vessel_types')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vessels');
    }
}
