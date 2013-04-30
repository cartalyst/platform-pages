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
			$table->string('type');
			$table->string('visibility');
			$table->text('groups')->nullable();

			// Database specific
			$table->text('template')->nullable();
			$table->text('value')->nullable();

			// Filesystem specific
			$table->string('file')->nullable();

			// Common
			$table->boolean('enabled');
			$table->timestamps();
			$table->unique('slug');
		});

		// @todo: Implement this
		// Schema::create('pages_groups', function($table)
		// {
		// 	$table->integer('page_id')->unsigned();
		// 	$table->integer('group_id')->unsigned();
		// 	$table->unique(array('page_id', 'group_id'));
		// });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pages');
		// Schema::drop('pages_groups');
	}

}
