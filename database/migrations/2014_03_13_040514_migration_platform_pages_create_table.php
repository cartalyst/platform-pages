<?php
/**
 * Part of the Platform Pages extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Pages extension
 * @version    1.0.8
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
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
			$table->text('roles')->nullable();

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
			$table->index('uri');
			$table->index('type');
			$table->index('visibility');
			$table->index('enabled');
		});

		// Create the meta attributes
		$attribute = app('Platform\Attributes\Repositories\AttributeRepositoryInterface');

		$attribute->create([
			'namespace'   => 'platform/pages',
			'name'        => 'Meta Title',
			'type'        => 'input',
			'slug'        => 'meta_title',
			'description' => 'Page meta title.',
			'enabled'     => 1,
		]);

		$attribute->create([
			'namespace'   => 'platform/pages',
			'name'        => 'Meta Description',
			'type'        => 'input',
			'slug'        => 'meta_description',
			'description' => 'Page meta description.',
			'enabled'     => 1,
		]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pages');

		$attribute = app('Platform\Attributes\Repositories\AttributeRepositoryInterface');

		$attributeIds = $attribute->createModel()->where('namespace', 'platform/pages')->lists('id');

		foreach ($attributeIds as $id)
		{
			$attribute->delete($id);
		}
	}

}
