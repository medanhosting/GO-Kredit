<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditAngsuranDetailTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_angsuran_detail', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nota_bayar_id')->nullable();
			$table->string('nomor_kredit');
			$table->datetime('tanggal');
			$table->integer('nth')->nullable();
			$table->string('tag');
			$table->double('amount');
			$table->text('description')->nullable();
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'nomor_kredit']);
            $table->index(['deleted_at', 'nota_bayar_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('k_angsuran_detail');
	}
}
