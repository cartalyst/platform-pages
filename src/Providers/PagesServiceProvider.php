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
 * @version    8.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Pages\Providers;

use Exception;
use Cartalyst\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PagesServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        // Get the Page model
        $model = $this->app['Platform\Pages\Models\Page'];

        // Register the menu page type
        $this->app['platform.menus.manager']->registerType(
            $this->app['platform.menus.types.page']
        );

        // Register the tags namespace
        $this->app['platform.tags.manager']->registerNamespace($model);

        // Register the attributes namespace
        $this->app['platform.attributes.manager']->registerNamespace($model);

        // Subscribe the registered event handler
        $this->app['events']->subscribe('platform.pages.handler.event');
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->prepareResources();

        // Register the repository
        $this->bindIf('platform.pages', 'Platform\Pages\Repositories\PageRepository');

        // Register the data handler
        $this->bindIf('platform.pages.handler.data', 'Platform\Pages\Handlers\DataHandler');

        // Register the validator
        $this->bindIf('platform.pages.validator', 'Platform\Pages\Validator\PagesValidator');

        // Register the event handler
        $this->bindIf('platform.pages.handler.event', 'Platform\Pages\Handlers\EventHandler');

        // Register the menus 'page' type
        $this->bindIf('platform.menus.types.page', 'Platform\Pages\Menus\PageType', true, false);
    }

    /**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        $config = realpath(__DIR__.'/../../resources/config/config.php');

        $this->mergeConfigFrom($config, 'platform.pages.config');

        $this->publishes([
            $config => config_path('platform-pages.php'),
        ], 'config');
    }
}
