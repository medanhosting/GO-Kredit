<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialmediaInstagramMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socialmedia_ig_media', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->string('ig_id');
            $table->string('type');
            $table->string('url');
            $table->text('tags')->nullable();
            $table->text('caption')->nullable();
            $table->string('link');
            $table->unsignedInteger('likes');
            $table->unsignedInteger('comments');
            $table->timestamp('posted_at');
            $table->timestamps();

            $table->index(['account_id', 'ig_id']);
            $table->index(['account_id', 'likes']);
            $table->index(['account_id', 'comments']);
            $table->index(['account_id', 'posted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socialmedia_ig_media');
    }
}
