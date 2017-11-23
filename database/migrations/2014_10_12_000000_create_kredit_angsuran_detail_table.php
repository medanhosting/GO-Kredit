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
			$table->integer('angsuran_id');
			$table->string('ref_id')->nullable();
			$table->string('tag')->nullable();
			$table->double('amount');
			$table->text('description')->nullable();
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'angsuran_id']);
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
