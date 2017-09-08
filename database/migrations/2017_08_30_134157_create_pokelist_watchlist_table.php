<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePokelistWatchlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socialmedia_pokelist_watchlist', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pokelist_id');
            $table->unsignedInteger('watchlist_id');
            $table->timestamps();
            $table->index(['pokelist_id']);
            $table->index(['watchlist_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socialmedia_pokelist_watchlist');
    }
}
