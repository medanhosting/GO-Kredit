<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPutusanField extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('p_putusan', function (Blueprint $table) {
			$table->string('nomor_kredit')->nullable();
			$table->double('biaya_notaris')->nullable();
			$table->double('persentasi_denda')->default(0.5);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('p_putusan', function (Blueprint $table) {
			// $table->dropColumn(['biaya_notaris', 'persentasi_denda']);
		});
	}
}
