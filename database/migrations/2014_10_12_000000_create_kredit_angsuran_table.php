<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKreditAngsuranTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('k_angsuran', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nomor_kredit');
			$table->datetime('issued_at');
			$table->datetime('paid_at')->nullable();
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'nomor_kredit']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('k_angsuran');
	}
}
