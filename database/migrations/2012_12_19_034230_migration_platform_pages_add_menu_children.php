<?php

use Illuminate\Database\Migrations\Migration;
use Platform\Menus\Menu;

class MigrationPlatformPagesAddMenuChildren extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$admin = Menu::adminMenu();

		$cms = Menu::find('admin-cms');

		$pages = new Menu(array(
			'slug'      => 'admin-pages',
			'extension' => 'platform/pages',
			'name'      => 'Pages',
			'driver'    => 'static',
			'class'     => 'icon-file',
			'uri'       => 'pages'
		));

		$pages->makeLastChildOf($cms);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$slugs = array('admin-cms-pages');

		foreach ($slugs as $slug)
		{
			if ($menu = Menu::find($slug))
			{
				$menu->delete();
			}
		}
	}

}
