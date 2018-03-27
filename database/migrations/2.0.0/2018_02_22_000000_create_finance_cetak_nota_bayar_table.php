<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceCetakNotaBayarTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('f_cetak_nota_bayar', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nomor_faktur');
			$table->datetime('tanggal');
			$table->text('karyawan')->nullable();
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'nomor_faktur', 'tanggal']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('f_cetak_nota_bayar');
	}
}
