<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditRekeningTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_rekening', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nama');
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'nama']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('k_rekening');
	}
}
