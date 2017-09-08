<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialMediaAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socialmedia_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('owner');
            $table->string('name');
            $table->string('type');
            $table->string('access_token')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('owner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socialmedia_accounts');
    }
}
