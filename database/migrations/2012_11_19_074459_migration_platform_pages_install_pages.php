<?php

use Illuminate\Database\Migrations\Migration;

class MigrationPlatformPagesInstallPages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('slug');
			$table->text('value')->nullable();
			$table->string('template');
			$table->boolean('status');
			$table->timestamps();

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
		Schema::drop('pages');
	}

}