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

return array(

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| This is your extension name and it is only used for presentational
	| purposes only.
	|
	*/

	'name' => 'Pages',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| This is your extension unique identifier and should not be changed as
	| it will be recognized as a new extension. Ideally, this should match
	| the folder structure within the extensions folder, but this is
	| completely optional.
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
	| You should list here all the extensions this extension requires to work
	| properly. This is used in conjunction with composer, so you should put
	| the same extension dependencies on your composer.json require key so
	| that they get resolved using composer, however you can use without
	| composer, at which point you'll have to ensure that the required
	| extensions are available.
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
	| Autoloading Logic for the Extension. It may either be 'composer', where
	| your composer.json file specifies the autoloading logic, 'platform',
	| where your extension receives convention autoloading based on Platform
	| standards, or a closure which takes two parameters, first is an instance of
	| Composer\Autoload\ClassLoader and second is Cartalyst\Extensions\Extension.
	| The autoload must set appropriate classes and namespaces available when the
	| extension is started.
	|
	*/

	'autoload' => 'composer',

	/*
	|--------------------------------------------------------------------------
	| URI
	|--------------------------------------------------------------------------
	|
	| Specify the URI that this extension will respond to. You can choose to
	| specify a single string, where the URI will be matched on the admin and
	| public sections of Platform. You can provide an array with keys 'admin'
	| and 'public' to specify a different URI for admin and public sections and
	| even provide an 'override' which is an array of Extensions this extension
	| overrides it's URI from.
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
	|	object Cartalyst\Extensions\ExtensionInterface
	|	object Illuminate\Foundation\Application
	|
	*/

	'register' => function(ExtensionInterface $extension, Application $app)
	{

		$app['platform/pages::page'] = function($app)
		{
			return new Platform\Pages\Models\Page;
		};

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
	|	object Cartalyst\Extensions\ExtensionInterface
	|	object Illuminate\Foundation\Application
	|
	*/

	'boot' => function(ExtensionInterface $extension, Application $app)
	{

		Platform\Pages\Models\Page::setThemeBag($app['themes']);
		Platform\Pages\Models\Page::setTheme($app['config']['cartalyst/themes::active']);

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
	|	object Cartalyst\Extensions\ExtensionInterface
	|	object Illuminate\Foundation\Application
	|
	*/

	'routes' => function(ExtensionInterface $extension, Application $app)
	{

		Route::get('/', 'Platform\Pages\Controllers\Frontend\PagesController@getPage');

		App::before(function($app)
		{
			Route::get('{slug}', 'Platform\Pages\Controllers\Frontend\PagesController@getPage')
				->where('slug', '.*?');
		});

	},

	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	|
	| List of permissions this extension has. These are shown in user
	| management to build a graphical interface where permissions may be
	| selected.
	|
	| The admin controllers state that permissions should follow the following
	| structure:
	|
	|     vendor/extension::admin.controller@method
	|
	| For example:
	|
	|    platform/users::admin.usersController@getIndex
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
	| Plugins
	|--------------------------------------------------------------------------
	|
	| List of custom plugins associated with the extension. Like routes, the
	| value for the plugin key may either be a closure or a class & method
	| name (joined with an @ symbol). Of course, Platform will guess the
	| plugin class for you, this is just for custom plugins or if you
	| do not wish to make a new class for a very small plugin.
	|
	*/

	'plugins' => array(),

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
				'name'    => 'Default Page (Shown on root route)',
				'config'  => 'platform/pages::default',
				'type'    => 'dropdown',
				'options' => function()
				{
					$response = API::get('pages');
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
				'name'    => 'Default Template (File pages)',
				'config'  => 'platform/pages::template',
				'type'    => 'dropdown',
				'options' => function()
				{
					return array(

						array(
							'value' => 'templates/default',
							'label' => 'Coming soon...',
						),

					);
				}
			),

		);
	},

);
