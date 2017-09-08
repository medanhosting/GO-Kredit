<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socialmedia_followers', function (Blueprint $table) {
            $table->increments('id');
            $table->Unsignedinteger('account_id');
            $table->string('user_id');
            $table->string('user_name');
            $table->string('user_data');
            $table->timestamp('follow_at');
            $table->timestamp('unfollow_at')->nullable();
            $table->text('data');
            $table->timestamps();

            $table->index(['account_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socialmedia_followers');
    }
}
