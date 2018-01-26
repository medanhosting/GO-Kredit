<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditPermintaanRestitusiTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_permintaan_restitusi', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nomor_kredit');
			$table->datetime('tanggal');
			$table->boolean('is_approved')->nullable();
			$table->string('nota_bayar_id')->nullable();
			$table->string('jenis');
			$table->double('amount');
			$table->text('alasan')->nullable();
			$table->text('karyawan')->nullable();
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
		Schema::dropIfExists('k_permintaan_restitusi');
	}
}