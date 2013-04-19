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
			$table->string('template');
			$table->string('type');
			$table->boolean('visibility');
			$table->text('groups')->nullable();
			$table->text('value')->nullable();
			$table->boolean('status');
			$table->timestamps();
			$table->unique('slug');
		});

		Schema::create('pages_groups', function($table)
		{
			$table->integer('page_id')->unsigned();
			$table->integer('group_id')->unsigned();
			$table->unique(array('page_id', 'group_id'));
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
		Schema::drop('pages_groups');
	}

}
