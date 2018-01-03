<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('visits', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('session');
			$table->string('ip', 15);
			$table->string('url');
			$table->date('date');
			$table->integer('count')->unsigned();

            $table->index('date');
            $table->index('session');
            $table->index('url');
            $table->index(['date', 'url']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('visits');
	}

}
