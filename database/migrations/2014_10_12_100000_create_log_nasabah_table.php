<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogNasabahTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('l_nasabah', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('parent_id')->nullable();
			$table->string('nik');
			$table->string('nama');
			$table->date('tanggal_lahir');
			$table->string('tempat_lahir');
			$table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
			$table->string('telepon');
			$table->string('status_perkawinan');
			$table->string('pekerjaan');
			$table->double('penghasilan_bersih');
			$table->text('alamat');
			$table->text('keluarga');
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
		Schema::dropIfExists('l_nasabah');
	}
}
