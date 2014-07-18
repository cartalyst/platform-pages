<?php namespace Platform\Pages\Database\Seeds;
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

use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder {

	/**
	 * {@inheritDoc}
	 */
	public function run()
	{
		// Create the meta attributes
		$attribute = app('Platform\Attributes\Repositories\AttributeRepositoryInterface');

		$attributeIds = $attribute->createModel()->where('namespace', 'platform/pages')->lists('id');

		foreach ($attributeIds as $id)
		{
			$attribute->delete($id);
		}

		$attribute->create([
			'namespace' => 'platform/pages',
			'name'      => 'Meta Title',
			'type'      => 'input',
			'slug'      => 'meta_title',
			'enabled'   => 1,
		]);

		$attribute->create([
			'namespace' => 'platform/pages',
			'name'      => 'Meta Description',
			'type'      => 'input',
			'slug'      => 'meta_description',
			'enabled'   => 1,
		]);

		// Create the welcome page, which will be the default
		// for a Platform installation.
		$page = app('Platform\Pages\Models\Page')->create([
			'name'             => 'Welcome',
			'slug'             => 'welcome',
			'uri'              => '/',
			'visibility'       => 'always',
			'meta_title'       => 'Welcome',
			'meta_description' => 'The default home page.',
			'type'             => 'filesystem',
			'file'             => 'welcome',
			'enabled'          => true,
		]);
	}

}
