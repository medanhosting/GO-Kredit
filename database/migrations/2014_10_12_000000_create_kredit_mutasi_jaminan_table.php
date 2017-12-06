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
			$table->string('nomor_kredit');
			$table->datetime('tanggal');
			$table->string('tag');
			$table->text('description')->nullable();
			$table->text('documents');
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
