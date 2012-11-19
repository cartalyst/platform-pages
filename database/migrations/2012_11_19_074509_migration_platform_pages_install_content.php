<?php

use Illuminate\Database\Migrations\Migration;

class MigrationPlatformPagesInstallContent extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('slug');
			$table->text('value')->nullable();
			$table->boolean('status');

			// Add a unique index on the
			// slug field.
			$table->unique('slug');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('content');
	}

}