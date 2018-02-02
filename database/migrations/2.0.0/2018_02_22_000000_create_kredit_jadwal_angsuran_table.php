<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditJadwalAngsuranTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_jadwal_angsuran', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nomor_kredit');
			$table->string('nomor_faktur')->nullable();
			$table->datetime('tanggal');
			$table->datetime('tanggal_bayar')->nullable();
			$table->integer('nth')->nullable();
			$table->double('jumlah');
			$table->text('deskripsi')->nullable();
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'nomor_kredit']);
            $table->index(['deleted_at', 'nomor_faktur']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('k_jadwal_angsuran');
	}
}
