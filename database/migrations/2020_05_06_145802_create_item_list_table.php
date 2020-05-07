<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemListTable extends Migration
{
    public function up()
    {
        Schema::create('item_list', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('list_id');
            $table->timestamps();
        });
    }
}
