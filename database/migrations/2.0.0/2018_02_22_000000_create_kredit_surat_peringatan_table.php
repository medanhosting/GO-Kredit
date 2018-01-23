<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditSuratPeringatanTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_surat_peringatan', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nomor_kredit');
			$table->integer('nth');
			$table->string('tag');
			$table->datetime('tanggal');
			$table->string('nip_karyawan');
			$table->string('penagihan_id')->nullable();
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
		Schema::dropIfExists('k_surat_peringatan');
	}
}