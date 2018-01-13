<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceCOATable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('f_coa', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('transaction_detail_id');
			$table->string('kode_akun');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'transaction_detail_id', 'kode_akun']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('f_coa');
	}
}
