<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialmediaWatchListsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socialmedia_ig_engagements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->string('type');
            $table->string('value');
            $table->timestamp('executed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();   
            $table->softDeletes();

            $table->index(['account_id', 'executed_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('socialmedia_ig_engagements');
    }
}
