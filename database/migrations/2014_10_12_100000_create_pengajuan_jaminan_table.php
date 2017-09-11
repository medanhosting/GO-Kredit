<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePengajuanJaminanTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('p_jaminan', function (Blueprint $table) {
			$table->increments('id');
			$table->enum('jenis', ['bpkb', 'shm', 'shgb']);
			$table->double('nilai_jaminan');
			$table->text('dokumen_jaminan');
			$table->string('pengajuan_id');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('p_jaminan');
	}
}
