<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemablesTable extends Migration
{
    public function up()
    {
        Schema::create('itemables', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('itemable_id');
            $table->string('itemable_type');
            $table->timestamps();
        });
    }
}
