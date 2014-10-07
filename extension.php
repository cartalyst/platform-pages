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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Foundation\Application;
use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Settings\Repository as Settings;
use Cartalyst\Permissions\Container as Permissions;

return [

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| Your extension name (it's only required for presentational purposes).
	|
	*/

	'name' => 'Pages',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| Your extension unique identifier and should not be changed as
	| it will be recognized as a whole new extension.
	|
	| Ideally, this should match the folder structure within the extensions
	| folder, but this is completely optional.
	|
	*/

	'slug' => 'platform/pages',

	/*
	|--------------------------------------------------------------------------
	| Author
	|--------------------------------------------------------------------------
	|
	| Because everybody deserves credit for their work, right?
	|
	*/

	'author' => 'Cartalyst LLC',

	/*
	|--------------------------------------------------------------------------
	| Description
	|--------------------------------------------------------------------------
	|
	| One or two sentences describing what the extension do for
	| users to view when they are installing the extension.
	|
	*/

	'description' => 'An extension to manage your website pages.',

	/*
	|--------------------------------------------------------------------------
	| Version
	|--------------------------------------------------------------------------
	|
	| Version should be a string that can be used with version_compare().
	|
	*/

	'version' => '1.0.0',

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| List here all the extensions that this extension requires to work.
	|
	| This is used in conjunction with composer, so you should put the
	| same extension dependencies on your main composer.json require
	| key, so that they get resolved using composer, however you
	| can use without composer, at which point you'll have to
	| ensure that the required extensions are available.
	|
	*/

	'require' => [

		'platform/access',
		'platform/content',

	],

	/*
	|--------------------------------------------------------------------------
	| Autoload Logic
	|--------------------------------------------------------------------------
	|
	| You can define here your extension autoloading logic, it may either
	| be 'composer', 'platform' or a 'Closure'.
	|
	| If composer is defined, your composer.json file specifies the autoloading
	| logic.
	|
	| If platform is defined, your extension receives convetion autoloading
	| based on the Platform standards.
	|
	| If a Closure is defined, it should take two parameters as defined
	| bellow:
	|
	|	object \Composer\Autoload\ClassLoader      $loader
	|	object \Illuminate\Foundation\Application  $app
	|
	| Supported: "composer", "platform", "Closure"
	|
	*/

	'autoload' => 'composer',

	/*
	|--------------------------------------------------------------------------
	| Service Providers
	|--------------------------------------------------------------------------
	|
	| Define your extension service providers here. They will be dynamically
	| registered without having to include them in app/config/app.php.
	|
	*/

	'providers' => [

		'Platform\Pages\Providers\PagesServiceProvider',

	],

	/*
	|--------------------------------------------------------------------------
	| Routes
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| any custom routing logic here.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'routes' => function(ExtensionInterface $extension, Application $app)
	{
		Route::group(['namespace' => 'Platform\Pages\Controllers'], function()
		{
			Route::group(['prefix' => admin_uri().'/pages', 'namespace' => 'Admin'], function()
			{
				Route::get('/', 'PagesController@index');
				Route::post('/', 'PagesController@executeAction');
				Route::get('grid', 'PagesController@grid');
				Route::get('create', 'PagesController@create');
				Route::post('create', 'PagesController@store');
				Route::get('{id}/edit', 'PagesController@edit');
				Route::post('{id}/edit', 'PagesController@update');
				Route::get('{id}/copy', 'PagesController@copy');
				Route::post('{id}/copy', 'PagesController@store');
				Route::get('{id}/delete', 'PagesController@delete');
			});

			Route::group(['prefix' => 'api/v1/pages', 'namespace' => 'Api\V1'], function()
			{
				Route::get('/', ['as' => 'api.v1.pages.all', 'uses' => 'PagesController@index']);
				Route::post('/', ['as' => 'api.v1.pages.create', 'uses' => 'PagesController@store']);
				Route::get('{id}', ['as' => 'api.v1.pages.show', 'uses' => 'PagesController@show']);
				Route::put('{id}', ['as' => 'api.v1.pages.update', 'uses' => 'PagesController@update']);
				Route::delete('{id}', ['as' => 'api.v1.pages.delete', 'uses' => 'PagesController@destroy']);
			});
		});

		Route::get('/', 'Platform\Pages\Controllers\Frontend\PagesController@page');

		$pages = $app['Platform\Pages\Repositories\PageRepositoryInterface'];

		foreach ($pages->findAllEnabled() as $page)
		{
			Route::get($page->uri, [
				'before' => $page->https ? 'https' : null,
				'uses'   => 'Platform\Pages\Controllers\Frontend\PagesController@page',
			]);
		}
	},

	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	|
	| Register here all the permissions that this extension has. These will
	| be shown in the user management area to build a graphical interface
	| where permissions can be selected to allow or deny user access.
	|
	| For detailed instructions on how to register the permissions, please
	| refer to the following url https://cartalyst.com/manual/permissions
	|
	*/

	'permissions' => function(Permissions $permissions)
	{
		$permissions->group('pages', function($g)
		{
			$g->name = 'Pages';

			$g->permission('pages.index', function($p)
			{
				$p->label = trans('platform/pages::permissions.index');

				$p->controller('Platform\Pages\Controllers\Admin\PagesController', 'index, grid');
			});

			$g->permission('pages.create', function($p)
			{
				$p->label = trans('platform/pages::permissions.create');

				$p->controller('Platform\Pages\Controllers\Admin\PagesController', 'create, store');
			});

			$g->permission('pages.copy', function($p)
			{
				$p->label = trans('platform/pages::permissions.copy');

				$p->controller('Platform\Pages\Controllers\Admin\PagesController', 'copy');
			});

			$g->permission('pages.edit', function($p)
			{
				$p->label = trans('platform/pages::permissions.edit');

				$p->controller('Platform\Pages\Controllers\Admin\PagesController', 'edit, update');
			});

			$g->permission('pages.delete', function($p)
			{
				$p->label = trans('platform/pages::permissions.delete');

				$p->controller('Platform\Pages\Controllers\Admin\PagesController', 'delete');
			});
		});
	},

	/*
	|--------------------------------------------------------------------------
	| Settings
	|--------------------------------------------------------------------------
	|
	| Register here all the settings that this extension has.
	|
	| For detailed instructions on how to register the settings, please
	| refer to the following url https://cartalyst.com/manual/settings
	|
	*/

	'settings' => function(Settings $settings)
	{
		$settings->find('platform')->section('pages', function($section)
		{
			$section->name = 'Pages';

			$section->fieldset('general', function($fieldset)
			{
				$fieldset->name = 'General';

				#
				$repository = app('Platform\Pages\Repositories\PageRepositoryInterface');
				$allPages = $repository->findAll();


				$fieldset->field('default_page', function($field) use ($allPages)
				{
					$field->name = 'Default Page';
					$field->config = 'platform/pages::default_page';
					$field->info = 'The page that is shown on the root route.';

					foreach ($allPages as $page)
					{
						$field->option($page->slug, function($option) use ($page)
						{
							$option->value = $page->slug;
							$option->label = $page->name;
						});
					}
				});

				$fieldset->field('default_section', function($field)
				{
					$field->name = 'Default Section';
					$field->config = 'platform/pages::default_section';
					$field->info = 'The default section when using the database storage type.';
				});

				$fieldset->field('default_template', function($field) use ($repository)
				{
					$field->name = 'Default Template';
					$field->config = 'platform/pages::default_template';
					$field->info = 'The default template that is used for pages.';

					foreach ($repository->templates() as $value => $label)
					{
						$field->option($value, function($option) use ($value, $label)
						{
							$option->value = $value;
							$option->label = $label;
						});
					}
				});

				$fieldset->field('not_found', function($field) use ($allPages)
				{
					$field->name = '404 Error Page';
					$field->config = 'platform/pages::not_found';
					$field->info = 'The page that is shown when a 404 error arises.';

					$field->option(null, function($option)
					{
						$option->value = null;
						$option->label = 'Default';
					});

					foreach ($allPages as $page)
					{
						$field->option($page->slug, function($option) use ($page)
						{
							$option->value = $page->slug;
							$option->label = $page->name;
						});
					}
				});
			});
		});
	},

	/*
	|--------------------------------------------------------------------------
	| Menus
	|--------------------------------------------------------------------------
	|
	| You may specify the default various menu hierarchy for your extension.
	|
	| You can provide a recursive array of menu children and their children.
	|
	| These will be created upon installation, synchronized upon upgrading
	| and removed upon uninstallation.
	|
	| Menu children are automatically put at the end of the menu for
	| extensions installed through the Operations extension.
	|
	| The default order (for extensions installed initially) can be
	| found by editing the file "app/config/platform.php".
	|
	*/

	'menus' => [

		'admin' => [

			[
				'slug'  => 'admin-pages',
				'name'  => 'Pages',
				'class' => 'fa fa-file',
				'uri'   => 'pages',
				'regex' => '/admin\/pages/i',
			],

		],

	],

	/*
	|--------------------------------------------------------------------------
	| Widgets
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is started. You can register
	| all your custom widgets here. Of course, Platform will guess the
	| widget class for you, this is just for custom widgets or if you
	| do not wish to make a new class for a very small widget.
	|
	*/

	'widgets' => function()
	{

	},

];
