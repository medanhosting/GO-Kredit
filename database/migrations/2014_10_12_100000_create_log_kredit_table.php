<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogKreditTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('l_kredit', function (Blueprint $table) {
			$table->increments('id');
			$table->string('pengajuan_id');
			$table->string('nasabah_id')->nullable();
			$table->string('jaminan_id');
			$table->string('jaminan_tipe');
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
		Schema::dropIfExists('l_kredit');
	}
}
