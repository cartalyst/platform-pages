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
use Platform\Ui\Models\Menu;

class MigrationPlatformPagesAddMenuChildren extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$admin = Menu::adminMenu();

		$pages = new Menu(array(
			'slug'      => 'admin-pages',
			'extension' => 'platform/pages',
			'name'      => 'Pages',
			'driver'    => 'static',
			'class'     => 'icon-file',
			'uri'       => 'pages'
		));

		$pages->makeLastChildOf($admin);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$slugs = array('admin-pages');

		foreach ($slugs as $slug)
		{
			if ($menu = Menu::find($slug))
			{
				$menu->delete();
			}
		}
	}

}
