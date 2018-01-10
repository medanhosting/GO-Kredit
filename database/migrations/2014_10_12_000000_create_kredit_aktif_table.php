<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditAktifTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_aktif', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nomor_kredit');
			$table->string('nomor_pengajuan');
			$table->string('kode_kantor');
			$table->string('jenis_pinjaman');
			$table->text('nasabah');
			$table->double('plafon_pinjaman');
			$table->double('suku_bunga');
			$table->integer('jangka_waktu');
			$table->integer('provisi');
			$table->double('administrasi');
			$table->double('legal');
			$table->datetime('tanggal');
			$table->text('ao');
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'nomor_pengajuan']);
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
		Schema::dropIfExists('k_aktif');
	}
}
