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

return array(

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| This is a name for your extension that is for presentational purposes
	| only.
	|
	*/

	'name' => 'Pages',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| This is a unique slug to describe the extension. This is the only
	| identifier for this extension and should not be changed as it will be
	| recognized as a new extension. Ideally, this should match the folder
	| structure within the extensions folder, though this is not required.
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
	| This is how we compare versions of extensions.
	|
	*/

	'version' => '2.0.0',

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| List all extensions this extension requires to install / run etc. This
	| is used in conjunction with composer (you should put the same extension
	| dependencies in composer.json so they're resolved using composer),
	| however it can be used without composer, at which point you'll have to
	| ensure the required extensions are available.
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

	'autoload' => 'platform',

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
	| Closure which takes a Cartalyst\Extensions\ExtensionInterface object as a
	| parameter and is called when the extension is registered. This can do
	| whatever custom logic is needed upon registering.
	|
	*/

	'register' => function(Cartalyst\Extensions\ExtensionInterface $extension, Illuminate\Foundation\Application $app)
	{

		$app['platform/pages::page'] = function($app)
		{
			return new Platform\Pages\Page;
		};

	},

	/*
	|--------------------------------------------------------------------------
	| Boot Callback
	|--------------------------------------------------------------------------
	|
	| Closure which takes a Cartalyst\Extensions\Extension object as a parameter
	| and is called when the extension is booted. This can do whatever custom
	| logic is needed upon booting.
	|
	*/

	'boot' => function(Cartalyst\Extensions\ExtensionInterface $extension, Illuminate\Foundation\Application $app)
	{

		require_once __DIR__.'/functions.php';

		Platform\Pages\Page::setThemeBag($app['themes']);
		Platform\Pages\Page::setTheme($app['config']['cartalyst/themes::active']);

	},

	/*
	|--------------------------------------------------------------------------
	| Routes
	|--------------------------------------------------------------------------
	|
	| Routes closure to be called when the Extension is started. Register any
	| custom routing logic here. This closure takes a
	| Cartalyst\Extensions\Extension object as a parameter just in case you
	| need it.
	|
	*/

	'routes' => function(Cartalyst\Extensions\ExtensionInterface $extension, Illuminate\Foundation\Application $app)
	{

		Route::get('/', 'Platform\Pages\Controllers\PagesController@getPage');

		App::before(function($app) {

			Route::get('{pageSlug}', 'Platform\Pages\Controllers\PagesController@getPage');

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

	'permissions' => array(),

	/*
	|--------------------------------------------------------------------------
	| Widgets
	|--------------------------------------------------------------------------
	|
	| List of custom widgets associated with the extension. Like routes, the
	| value for the widget key may either be a closure or a class & method name
	| (joined with an @ symbol). Of course, Platform will guess the widget
	| class for you, this is just for custom widgets or if you do not wish to
	| make a new class for a very small widget.
	|
	*/

	'widgets' => array(),

	/*
	|--------------------------------------------------------------------------
	| Plugins
	|--------------------------------------------------------------------------
	|
	| List of custom plugins associated with the extension. Like routes, the
	| value for the plugin key may either be a closure or a class & method name
	| (joined with an @ symbol). Of course, Platform will guess the plugin
	| class for you, this is just for custom plugins or if you do not wish to
	| make a new class for a very small plugin.
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

	'settings' => array(),

);
