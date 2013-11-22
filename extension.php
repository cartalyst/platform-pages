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

use Cartalyst\Extensions\ExtensionInterface;
use Illuminate\Foundation\Application;
use Platform\Menus\Models\Menu;
use Platform\Pages\Controllers\Frontend\PagesController;
use Platform\Pages\Models\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return array(

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

	'require' => array(

		'platform/admin',
		'platform/content',

	),

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
	|
	| Supported: "composer", "platform", "Closure"
	|
	*/

	'autoload' => 'composer',

	/*
	|--------------------------------------------------------------------------
	| URI
	|--------------------------------------------------------------------------
	|
	| You can specify the URI that this extension will respond to.
	|
	| You can choose to specify a single string, where the URI will be matched
	| on the 'admin' and 'public' sections of Platform.
	|
	| You can provide an array with the 'admin' and 'public' keys to specify
	| a different URI for admin and public sections, you can have as many
	| keys as you need in case your applications needs them.
	|
	| You can provide an 'override' which is an array of extensions this
	| extension overrides it's URI from.
	|
	*/

	'uri' => 'pages',

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
		Route::group(array('prefix' => '{api}/v1/page'), function()
		{
			Route::get('{slug}'   , 'Platform\Pages\Controllers\Api\V1\PagesController@show')
				->where('slug', '.*?');
			Route::delete('{slug}', 'Platform\Pages\Controllers\Api\V1\PagesController@destroy');
		});

		foreach (app('Platform\Pages\Models\Page')->all() as $page)
		{
			Route::get($page->uri, 'Platform\Pages\Controllers\Frontend\PagesController@getPage');
		}
	},

	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	|
	| List of permissions this extension has. These are shown in the user
	| management area to build a graphical interface where permissions
	| may be selected.
	|
	| The admin controllers state that permissions should follow the following
	| structure:
	|
	|     vendor/extension::area.controller@method
	|
	| For example:
	|
	|    platform/users::admin.usersController@index
	|    Platform\Users\Controllers\Admin\UsersController@getIndex
	|
	| These are automatically generated for controller routes however you are
	| free to add your own permissions and check against them at any time.
	|
	| When writing permissions, if you put a 'key' => 'value' pair, the 'value'
	| will be the label for the permission which is displayed when editing
	| permissions.
	|
	*/

	'permissions' => function()
	{
		return array(

			'platform/pages::admin.pagesController@index'  => Lang::get('platform/pages::permissions.index'),
			'platform/pages::admin.pagesController@grid'   => Lang::get('platform/pages::permissions.grid'),
			'platform/pages::admin.pagesController@create' => Lang::get('platform/pages::permissions.create'),
			'platform/pages::admin.pagesController@copy'   => Lang::get('platform/pages::permissions.copy'),
			'platform/pages::admin.pagesController@edit'   => Lang::get('platform/pages::permissions.edit'),
			'platform/pages::admin.pagesController@delete' => Lang::get('platform/pages::permissions.delete'),

		);
	},

	/*
	|--------------------------------------------------------------------------
	| Widgets
	|--------------------------------------------------------------------------
	|
	| List of custom widgets associated with the extension. Like routes, the
	| value for the widget key may either be a closure or a class & method
	| name (joined with an @ symbol). Of course, Platform will guess the
	| widget class for you, this is just for custom widgets or if you
	| do not wish to make a new class for a very small widget.
	|
	*/

	'widgets' => array(),

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
		return array(

			'pages' => array('name' => 'Pages'),

			'pages::general' => array('name' => 'General'),

			'pages::general.default' => array(
				'name'    => 'Default Page',
				'config'  => 'platform/pages::default',
				'info'    => 'The page that is shown on the root route.',
				'type'    => 'dropdown',
				'options' => function()
				{
					$response = API::get('v1/pages');
					$pages    = $response['pages'];

					$options  = array();

					foreach ($pages as $page)
					{
						$options[] = array(
							'value' => $page->slug,
							'label' => $page->name,
						);
					}

					return $options;
				}
			),

			'pages::general.template' => array(
				'name'    => 'Default Template',
				'config'  => 'platform/pages::template',
				'info'    => 'The default template that is used for pages.',
				'type'    => 'dropdown',
				'options' => function()
				{
					$options = array();

					foreach (Page::getTemplates() as $value => $label)
					{
						$options[] = array(
							'value' => $value,
							'label' => $label,
						);
					}

					return $options;
				}
			),

			'pages::general.not_found' => array(
				'name'    => '404 Error Page',
				'config'  => 'platform/pages::not_found',
				'info'    => 'The page that is shown when a 404 error arises.',
				'type'    => 'dropdown',
				'options' => function()
				{
					$response = API::get('v1/pages');
					$pages    = $response['pages'];

					$options  = array();

					$options[] = array(
						'value' => null,
						'label' => 'Default',
					);

					foreach ($pages as $page)
					{
						$options[] = array(
							'value' => $page->slug,
							'label' => $page->name,
						);
					}

					return $options;
				}
			),

		);
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

	'menus' => array(

		'admin' => array(

			array(
				'slug'  => 'admin-pages',
				'name'  => 'Pages',
				'class' => 'fa fa-file',
				'uri'   => 'pages',
				'regex' => '/admin\/pages/i',
			),

		),

	),

);
