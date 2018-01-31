<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceJurnalTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('f_jurnal', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('detail_transaksi_id');
			$table->integer('coa_id');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'detail_transaksi_id', 'coa_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('f_jurnal');
	}
}
