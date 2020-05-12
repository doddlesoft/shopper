<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListMealTable extends Migration
{
    public function up()
    {
        Schema::create('list_meal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('list_id');
            $table->unsignedBigInteger('meal_id');
            $table->timestamps();
        });
    }
}
