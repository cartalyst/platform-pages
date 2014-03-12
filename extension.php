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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Extensions\ExtensionInterface;
use Illuminate\Foundation\Application;
use Platform\Pages\Controllers\Frontend\PagesController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return [

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| This is your extension name and it is only required for
	| presentational purposes.
	|
	*/

	'name' => 'Pages',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| This is your extension unique identifier and should not be changed as
	| it will be recognized as a new extension.
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
	| One or two sentences describing the extension for users to view when
	| they are installing the extension.
	|
	*/

	'description' => 'An extension to manage your website pages.',

	/*
	|--------------------------------------------------------------------------
	| Version
	|--------------------------------------------------------------------------
	|
	| Version should be a string that can be used with version_compare().
	| This is how the extensions versions are compared.
	|
	*/

	'version' => '2.0.0',

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| List here all the extensions that this extension requires to work.
	| This is used in conjunction with composer, so you should put the
	| same extension dependencies on your main composer.json require
	| key, so that they get resolved using composer, however you
	| can use without composer, at which point you'll have to
	| ensure that the required extensions are available.
	|
	*/

	'require' => [

		'platform/admin',
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
	| Register Callback
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is registered. This can do
	| all the needed custom logic upon registering.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'register' => function(ExtensionInterface $extension, Application $app)
	{
		$app['Platform\Menus\PageType'] = $app->share(function($app)
		{
			return new Platform\Pages\Menus\PageType($app['url'], $app['view'], $app['translator']);
		});

		$app->bind('Platform\Pages\Repositories\PageRepositoryInterface', function($app)
		{
			return new Platform\Pages\Repositories\DbPageRepository(get_class($app['Platform\Pages\Models\Page']));
		});
	},

	/*
	|--------------------------------------------------------------------------
	| Boot Callback
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is booted. This can do
	| all the needed custom logic upon booting.
	|
	| The closure parameters are:
	|
	|	object \Cartalyst\Extensions\ExtensionInterface  $extension
	|	object \Illuminate\Foundation\Application        $app
	|
	*/

	'boot' => function(ExtensionInterface $extension, Application $app)
	{
		// Set the theme bag and the active theme
		app('Platform\Pages\Models\Page')->setThemeBag($app['themes']);
		app('Platform\Pages\Models\Page')->setTheme($app['config']['cartalyst/themes::active']);

		// Register a new attribute namespace
		app('Platform\Attributes\Models\Attribute')->registerNamespace(app('Platform\Pages\Models\Page'));

		// Check the environment and app.debug settings
		if ($app->environment() === 'production' or $app['config']['app.debug'] === false)
		{
			$notFound = $app['config']['platform/pages::not_found'];

			if ( ! is_null($notFound))
			{
				$app->error(function(NotFoundHttpException $exception, $code) use ($notFound)
				{
					Log::error($exception);

					try
					{
						$content = with(new PagesController)->getPage($notFound);

						return Response::make($content, 404);
					}
					catch (Exception $e)
					{

					}
				});
			}
		}

		// Register the menu page type
		app('Platform\Menus\Models\Menu')->registerType($app['Platform\Menus\PageType']);
	},

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
		Route::group(['prefix' => admin_uri().'/pages', 'namespace' => 'Platform\Pages\Controllers\Admin'], function()
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

		Route::group(['prefix' => 'api/v1/pages', 'namespace' => 'Platform\Pages\Controllers\Api\V1'], function()
		{
			Route::get('/', 'PagesController@index');
			Route::post('/', 'PagesController@store');
			Route::get('{id}', 'PagesController@show');
			Route::put('{id}', 'PagesController@update');
			Route::delete('{id}', 'PagesController@destroy');
		});

		App::before(function()
		{
			$pages = app('Platform\Pages\Repositories\PageRepositoryInterface');

			foreach ($pages->findAllEnabled() as $page)
			{
				Route::get($page->uri, 'Platform\Pages\Controllers\Frontend\PagesController@page');
			}
		});
	},

	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	|
	| List of permissions this extension has. These are shown in the user
	| management area to build a graphical interface where permissions
	| can be selected to allow or deny user access.
	|
	| You can protect single or multiple controller methods at once.
	|
	| When writing permissions, if you put a 'key' => 'value' pair, the 'value'
	| will be the label for the permission which is going to be displayed
	| when editing the permissions and when access is denied.
	|
	| The permissions should follow the following structure:
	|
	|     Vendor\Namespace\Controller@method
	|     Vendor\Namespace\Controller@method1,method2, ...
	|
	*/

	'permissions' => function()
	{
		return [

			'Platform\Pages\Controllers\Admin\PagesController@index,grid' => Lang::get('platform/pages::permissions.index'),
			'Platform\Pages\Controllers\Admin\PagesController@create'     => Lang::get('platform/pages::permissions.create'),
			'Platform\Pages\Controllers\Admin\PagesController@copy'       => Lang::get('platform/pages::permissions.copy'),
			'Platform\Pages\Controllers\Admin\PagesController@edit'       => Lang::get('platform/pages::permissions.edit'),
			'Platform\Pages\Controllers\Admin\PagesController@delete'     => Lang::get('platform/pages::permissions.delete'),

		];
	},

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

	/*
	|--------------------------------------------------------------------------
	| Settings
	|--------------------------------------------------------------------------
	|
	| Register any settings for your extension. You may can also configure
	| the namespace and group that a setting belongs to.
	|
	*/

	'settings' => function()
	{
		return [

			'pages' => ['name' => 'Pages'],

			'pages::general' => ['name' => 'General'],

			'pages::general.default_page' => [
				'name'    => 'Default Page',
				'config'  => 'platform/pages::default_page',
				'info'    => 'The page that is shown on the root route.',
				'type'    => 'dropdown',
				'options' => function()
				{
					$options = [];

					$page = app('Platform\Pages\Repositories\PageRepositoryInterface');

					foreach ($page->findAll() as $page)
					{
						$options[] = [
							'value' => $page->slug,
							'label' => $page->name,
						];
					}

					return $options;
				}
			],

			'pages::general.default_section' => [
				'name'    => 'Default Section',
				'config'  => 'platform/pages::default_section',
				'info'    => 'The default section when using the database storage type.',
				'type'    => 'text',
			],

			'pages::general.default_template' => [
				'name'    => 'Default Template',
				'config'  => 'platform/pages::default_template',
				'info'    => 'The default template that is used for pages.',
				'type'    => 'dropdown',
				'options' => function()
				{
					$options = [];

					$page = app('Platform\Pages\Repositories\PageRepositoryInterface');

					foreach ($page->templates() as $value => $label)
					{
						$options[] = compact('value', 'label');
					}

					return $options;
				}
			],

			'pages::general.not_found' => [
				'name'    => '404 Error Page',
				'config'  => 'platform/pages::not_found',
				'info'    => 'The page that is shown when a 404 error arises.',
				'type'    => 'dropdown',
				'options' => function()
				{
					$page = app('Platform\Pages\Repositories\PageRepositoryInterface');

					$options = [];

					$options[] = [
						'value' => null,
						'label' => 'Default',
					];

					foreach ($page->findAll() as $page)
					{
						$options[] = [
							'value' => $page->slug,
							'label' => $page->name,
						];
					}

					return $options;
				}
			],

		];
	},

	/*
	|--------------------------------------------------------------------------
	| Menus
	|--------------------------------------------------------------------------
	|
	| You may specify the default various menu hierarchy for your extension.
	| You can provide a recursive array of menu children and their children.
	| These will be created upon installation, synchronized upon upgrading
	| and removed upon uninstallation.
	|
	| Menu children are automatically put at the end of the menu for extensions
	| installed through the Operations extension.
	|
	| The default order (for extensions installed initially) can be
	| found by editing app/config/platform.php.
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

];
