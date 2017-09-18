<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogShgbTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('l_shgb', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('parent_id')->nullable();
			$table->string('tipe');
			$table->string('nomor_sertifikat');
			$table->string('masa_berlaku_sertifikat');
			$table->string('atas_nama');
			$table->integer('luas_tanah');
			$table->integer('luas_bangunan')->nullable();
			$table->string('alamat');
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
		Schema::dropIfExists('l_shgb');
	}
}
