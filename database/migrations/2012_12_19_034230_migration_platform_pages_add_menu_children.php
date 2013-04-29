<?php

use Illuminate\Database\Migrations\Migration;
use Platform\Ui\Menu;

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

		$pages->makeFirstChildOf($admin);
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
