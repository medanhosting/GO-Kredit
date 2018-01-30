<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditNotaBayarTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_nota_bayar', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nomor_kredit');
			$table->string('nomor_faktur');
			$table->string('penagihan_id')->nullable();
			$table->datetime('tanggal');
			$table->double('jumlah')->nullable();
			$table->string('nip_karyawan');
			$table->string('nomor_perkiraan')->nullable();
			$table->string('jenis')->nullable();
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'nip_karyawan']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('k_nota_bayar');
	}
}
