<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditJaminanTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_jaminan', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nomor_kredit');
			$table->string('nomor_jaminan');
			$table->string('kategori');
			$table->text('dokumen');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'nomor_kredit', 'nomor_jaminan']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('k_jaminan');
	}
}
