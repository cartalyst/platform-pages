<?php
/**
 * Part of the Platform Pages extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Pages extension
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Database\Migrations\Migration;

class MigrationPlatformPagesCreateTable extends Migration {

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
			$table->string('uri');
			$table->string('type');
			$table->string('visibility');
			$table->text('groups')->nullable();

			// Database specific
			$table->string('template')->nullable();
			$table->string('section')->nullable();
			$table->text('value')->nullable();

			// Filesystem specific
			$table->string('file')->nullable();

			// Common
			$table->boolean('enabled');
			$table->boolean('https')->default(0);
			$table->timestamps();

			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
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
