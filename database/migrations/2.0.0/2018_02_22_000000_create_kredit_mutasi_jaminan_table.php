<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditMutasiJaminanTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_mutasi_jaminan', function (Blueprint $table) {
			$table->increments('id');
			$table->string('mutasi_jaminan_id')->nullable();
			$table->string('nomor_kredit');
			$table->string('nomor_jaminan');
			$table->datetime('tanggal');
			$table->string('kategori');
			$table->string('tag');
			$table->string('status');
			$table->text('deskripsi')->nullable();
			$table->text('dokumen');
			$table->text('karyawan');
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'nomor_kredit']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('k_mutasi_jaminan');
	}
}
