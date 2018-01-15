<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceAccountTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('f_account', function (Blueprint $table) {
			$table->increments('id');
			$table->string('kode_kantor')->nullable();
			$table->string('akun_id')->nullable();
			$table->string('akun')->nullable();
			$table->string('nomor_perkiraan')->nullable();
			$table->string('mata_uang')->default('IDR');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'kode_kantor', 'akun_id']);
			$table->index(['deleted_at', 'kode_kantor', 'nomor_perkiraan']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('f_account');
	}
}
