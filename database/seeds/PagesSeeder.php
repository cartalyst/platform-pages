<?php namespace Platform\Pages\Database\Seeds;
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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class PagesSeeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// Create the meta attributes
		$attribute = app('Platform\Attributes\Repositories\AttributeRepositoryInterface');

		$attribute->create(array(
			'namespace' => 'platform/pages',
			'name'      => 'Meta Title',
			'type'      => 'input',
			'slug'      => 'meta_title',
			'enabled'   => 1,
		));
		$attribute->create(array(
			'namespace' => 'platform/pages',
			'name'      => 'Meta Description',
			'type'      => 'input',
			'slug'      => 'meta_description',
			'enabled'   => 1,
		));

		// Create the welcome page, which will be the default
		// for a Platform installation.
		$page = app('Platform\Pages\Models\Page')->create(array(
			'name'             => 'Welcome',
			'slug'             => 'welcome',
			'uri'              => '/',
			'visibility'       => 'always',
			'meta_title'       => 'Welcome',
			'meta_description' => 'The default home page.',
			'type'             => 'filesystem',
			'file'             => 'welcome',
			'enabled'          => true,
		));
	}
}
