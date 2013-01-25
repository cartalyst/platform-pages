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
