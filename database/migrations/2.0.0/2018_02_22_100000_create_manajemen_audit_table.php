<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManajemenAuditTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('m_audit', function (Blueprint $table) {
			$table->increments('id');
			$table->datetime('tanggal');
			$table->string('kode_kantor')->nullable();
			$table->string('domain');
			$table->text('data_lama');
			$table->text('data_perubahan');
			$table->text('data_baru');
			$table->text('karyawan');
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'tanggal', 'domain']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('m_audit');
	}
}
