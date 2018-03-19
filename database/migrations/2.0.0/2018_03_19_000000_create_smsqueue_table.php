<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsqueueTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sms_queues', function (Blueprint $table) {
			$table->increments('id');
			$table->string('morph_reference_id');
			$table->string('morph_reference_tag');
			$table->text('penerima');
			$table->text('isi');
			$table->string('status');
			$table->text('respon')->nullable();

			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'morph_reference_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('sms_queues');
	}
}
