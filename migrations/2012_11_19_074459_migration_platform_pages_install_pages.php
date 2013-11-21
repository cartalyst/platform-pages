<?php
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Database\Migrations\Migration;
use Platform\Pages\Models\Page;

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
			$table->string('uri');
			$table->string('type');
			$table->string('visibility');
			$table->text('groups')->nullable();
			$table->string('meta_description');
			$table->string('meta_title');

			// Database specific
			$table->string('template')->nullable();
			$table->string('section')->nullable();
			$table->text('value')->nullable();

			// Filesystem specific
			$table->string('file')->nullable();

			// Common
			$table->boolean('enabled');
			$table->timestamps();

			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
			$table->unique('slug');
		});

		// Create the welcome page, which will be the default
		// for a Platform installation.
		$page = new Page(array(
			'name'              => 'Welcome',
			'slug'              => 'welcome',
			'uri'               => '/',
			'visibility'        => 'always',
			'meta_title'        => 'Welcome',
			'meta_description'  => 'The default home page.',
			'type'              => 'filesystem',
			'file'              => 'welcome',
			'enabled'           => true,
		));
		$page->save();
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
