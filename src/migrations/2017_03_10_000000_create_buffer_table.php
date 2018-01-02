<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBufferTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('visits_buffer', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('session');
			$table->string('ip', 15);
			$table->string('url');
			$table->date('date');
			$table->integer('count')->unsigned();

            $table->index(['session', 'ip', 'url', 'date']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('visits_buffer');
	}
}
