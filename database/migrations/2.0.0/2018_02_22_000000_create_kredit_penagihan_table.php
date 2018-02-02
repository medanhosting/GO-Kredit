<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditPenagihanTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_penagihan', function (Blueprint $table) {
			$table->increments('id');
			$table->string('surat_peringatan_id')->nullable();
			$table->string('nomor_kredit');
			$table->string('nomor_faktur')->nullable();
			$table->string('tag');
			$table->text('penerima')->nullable();
			$table->double('jumlah')->nullable();
			$table->datetime('tanggal')->nullable();
			$table->string('karyawan')->nullable();
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
		Schema::dropIfExists('k_penagihan');
	}
}
