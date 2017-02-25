<?php

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
 * @version    5.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Settings\Repository as Settings;
use Illuminate\Contracts\Foundation\Application;
use Cartalyst\Permissions\Container as Permissions;
use Illuminate\Contracts\Routing\Registrar as Router;

return [

    /*
    |--------------------------------------------------------------------------
    | Slug
    |--------------------------------------------------------------------------
    |
    | This is the extension unique identifier and should not be
    | changed as it will be recognized as a new extension.
    |
    | Note:
    |
    |   Ideally this should match the folder structure within the
    |   extensions folder, however this is completely optional.
    |
    */

    'slug' => 'platform/pages',

    /*
    |--------------------------------------------------------------------------
    | Name
    |--------------------------------------------------------------------------
    |
    | This is the extension name, used mainly for presentational purposes.
    |
    */

    'name' => 'Pages',

    /*
    |--------------------------------------------------------------------------
    | Description
    |--------------------------------------------------------------------------
    |
    | A brief sentence describing what the extension does.
    |
    */

    'description' => 'An extension to manage your website pages.',

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | This is the extension version and it should be set as a string
    | so it can be used with the version_compare() function.
    |
    */

    'version' => '5.0.0',

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
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Define here all the extensions that this extension depends on to work.
    |
    | Note:
    |
    |   This is used in conjunction with Composer, so you should put the
    |   exact same dependencies on the extension composer.json require
    |   array, so that they get resolved automatically by Composer.
    |
    |   However you can use without Composer, at which point you will
    |   have to ensure that the required extensions are available!
    |
    */

    'requires' => [

        'platform/access',
        'platform/content',
        'platform/tags',

    ],

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | Define here your extension service providers. They will be dynamically
    | registered without having to include them in config/app.php file.
    |
    */

    'providers' => [

        Platform\Pages\Providers\PagesServiceProvider::class,

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
    |   object \Illuminate\Contracts\Routing\Registrar  $router
    |	object \Cartalyst\Extensions\ExtensionInterface  $extension
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    */

    'routes' => function (Router $router, ExtensionInterface $extension, Application $app) {
        if (! $app->routesAreCached()) {
            $router->group(['namespace' => 'Platform\Pages\Controllers'], function (Router $router) use ($app) {
                $router->group([
                    'prefix' => admin_uri().'/pages', 'namespace' => 'Admin'
                ], function (Router $router) {
                    $router->get('/', 'PagesController@index')->name('admin.pages.all');
                    $router->post('/', 'PagesController@executeAction')->name('admin.pages.all');

                    $router->get('grid', 'PagesController@grid')->name('admin.pages.grid');

                    $router->get('create', 'PagesController@create')->name('admin.pages.create');
                    $router->post('create', 'PagesController@store')->name('admin.pages.create');

                    $router->get('{id}', 'PagesController@edit')->name('admin.pages.edit');
                    $router->post('{id}', 'PagesController@update')->name('admin.pages.edit');
                    $router->delete('{id}', 'PagesController@delete')->name('admin.pages.delete');

                    $router->get('{id}/copy', 'PagesController@copy')->name('admin.pages.copy');
                    $router->post('{id}/copy', 'PagesController@store')->name('admin.pages.copy');
                });

                foreach ($app['platform.pages']->findAllEnabled() as $page) {
                    $router->get($page->uri, 'Frontend\PagesController@page');
                }
            });
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
    | The closure parameters are:
    |
    |   object \Cartalyst\Permissions\Container  $permissions
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    */

    'permissions' => function (Permissions $permissions, Application $app) {
        $permissions->group('pages', function ($g) {
            $g->name = 'Pages';

            $g->permission('pages.index', function ($p) {
                $p->label = trans('platform/pages::permissions.index');

                $p->controller('Platform\Pages\Controllers\Admin\PagesController', 'index, grid');
            });

            $g->permission('pages.create', function ($p) {
                $p->label = trans('platform/pages::permissions.create');

                $p->controller('Platform\Pages\Controllers\Admin\PagesController', 'create, store');
            });

            $g->permission('pages.copy', function ($p) {
                $p->label = trans('platform/pages::permissions.copy');

                $p->controller('Platform\Pages\Controllers\Admin\PagesController', 'copy');
            });

            $g->permission('pages.edit', function ($p) {
                $p->label = trans('platform/pages::permissions.edit');

                $p->controller('Platform\Pages\Controllers\Admin\PagesController', 'edit, update');
            });

            $g->permission('pages.delete', function ($p) {
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
    | The closure parameters are:
    |
    |   object \Cartalyst\Settings\Repository  $settings
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    */

    'settings' => function (Settings $settings, Application $app) {
        $settings->find('platform')->section('pages', function ($section) {
            $section->name = 'Pages';

            $section->fieldset('general', function ($fieldset) {
                $fieldset->name = 'General';

                #
                $repository = app('Platform\Pages\Repositories\PageRepositoryInterface');
                $allPages = $repository->findAll();


                $fieldset->field('default_page', function ($field) use ($allPages) {
                    $field->name = 'Default Page';
                    $field->config = 'platform.pages.config.default_page';
                    $field->info = 'The page that is shown on the root route.';

                    foreach ($allPages as $page) {
                        $field->option($page->slug, function ($option) use ($page) {
                            $option->value = $page->slug;
                            $option->label = $page->name;
                        });
                    }
                });

                $fieldset->field('default_section', function ($field) {
                    $field->name = 'Default Section';
                    $field->config = 'platform.pages.config.default_section';
                    $field->info = 'The default section when using the database storage type.';
                });

                $fieldset->field('default_template', function ($field) use ($repository) {
                    $field->name = 'Default Template';
                    $field->config = 'platform.pages.config.default_template';
                    $field->info = 'The default template that is used for pages.';

                    foreach ($repository->templates() as $value => $label) {
                        $field->option($value, function ($option) use ($value, $label) {
                            $option->value = $value;
                            $option->label = $label;
                        });
                    }
                });

                $fieldset->field('not_found', function ($field) use ($allPages) {
                    $field->name = '404 Error Page';
                    $field->config = 'platform.pages.config.not_found';
                    $field->info = 'The page that is shown when a 404 error arises.';

                    $field->option(null, function ($option) {
                        $option->value = null;
                        $option->label = 'Default';
                    });

                    foreach ($allPages as $page) {
                        $field->option($page->slug, function ($option) use ($page) {
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
    | found by editing the file "config/platform.php".
    |
    */

    'menus' => [

        'admin' => [

            [
                'slug'  => 'admin-pages',
                'name'  => 'Pages',
                'class' => 'fa fa-file',
                'uri'   => 'pages',
                'regex' => '/:admin\/pages/i',
            ],

        ],

    ],

];
