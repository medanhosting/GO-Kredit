<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceTransactionDetailTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('f_transaction_detail', function (Blueprint $table) {
			$table->increments('id');
			$table->datetime('tanggal');
			$table->double('amount');
			$table->string('morph_reference_tag');
			$table->string('morph_reference_id');
			$table->text('description');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'tanggal']);
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
		Schema::dropIfExists('f_transaction_detail');
	}
}
