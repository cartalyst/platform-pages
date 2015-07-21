<?php namespace Platform\Pages\Database\Seeds;
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
 * @version    2.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder {

	/**
	 * {@inheritDoc}
	 */
	public function run()
	{
		// Prologue.
		$page = app('Platform\Pages\Models\Page')->create([
			'name'             => 'About',
			'slug'             => 'about',
			'uri'              => '/about',
			'visibility'       => 'always',
			'meta_title'       => 'About',
			'meta_description' => 'About Platform',
			'type'             => 'filesystem',
			'file'             => 'about',
			'enabled'          => true,
		]);

		// Default.
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
