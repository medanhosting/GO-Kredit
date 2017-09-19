<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogShmTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('l_shm', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('parent_id')->nullable();
			$table->string('tipe')->nullable();
			$table->string('nomor_sertifikat')->nullable();
			$table->string('atas_nama')->nullable();
			$table->integer('luas_tanah')->nullable();
			$table->integer('luas_bangunan')->nullable();
			$table->string('alamat')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('l_shm');
	}
}
