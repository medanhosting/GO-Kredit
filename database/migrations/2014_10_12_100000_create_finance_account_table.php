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
			$table->string('akun')->nullable();
			$table->string('kode_akun')->nullable();
			$table->boolean('is_pasiva');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'kode_kantor', 'kode_akun']);
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
