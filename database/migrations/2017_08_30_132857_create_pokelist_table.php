<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePokelistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socialmedia_pokelist', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->string('user_id');
            $table->string('fullname');
            $table->string('username');
            $table->string('is_active')->default(0);
            $table->timestamps();

            // $table->index(['watchlist_id']);
            $table->unique(['account_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socialmedia_pokelist');
    }
}
