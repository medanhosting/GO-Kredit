<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceNotaBayarTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('f_nota_bayar', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nomor_faktur');
			$table->string('morph_reference_id')->nullable();
			$table->string('morph_reference_tag')->nullable();
			$table->datetime('tanggal');
			$table->string('jenis');
			$table->text('karyawan')->nullable();
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'morph_reference_tag']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('f_nota_bayar');
	}
}
