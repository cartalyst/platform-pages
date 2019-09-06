<?php

/*
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

namespace Platform\Pages\Handlers;

use Platform\Pages\Models\Page;
use Illuminate\Contracts\Events\Dispatcher;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class EventHandler extends BaseEventHandler implements EventHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen('platform.page.creating', __CLASS__.'@creating');
        $dispatcher->listen('platform.page.created', __CLASS__.'@created');

        $dispatcher->listen('platform.page.updating', __CLASS__.'@updating');
        $dispatcher->listen('platform.page.updated', __CLASS__.'@updated');

        $dispatcher->listen('platform.page.deleted', __CLASS__.'@deleted');
    }

    /**
     * {@inheritdoc}
     */
    public function creating(array $input)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function created(Page $page)
    {
        $this->flushCache($page);
    }

    /**
     * {@inheritdoc}
     */
    public function updating(Page $page, array $input)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function updated(Page $page)
    {
        $this->flushCache($page);
    }

    /**
     * {@inheritdoc}
     */
    public function deleted(Page $page)
    {
        $this->flushCache($page);
    }

    /**
     * Flush the cache.
     *
     * @param \Platform\Pages\Models\Page $page
     *
     * @return void
     */
    protected function flushCache(Page $page)
    {
        $cacheKeys = [$page->id, $page->slug, $page->uri];

        $this->app['cache']->forget('platform.pages.all');
        $this->app['cache']->forget('platform.pages.all.enabled');

        $this->app['cache']->forget('platform.page.'.$cacheKeys[0]);
        $this->app['cache']->forget('platform.page.slug.'.$cacheKeys[1]);
        $this->app['cache']->forget('platform.page.uri.'.$cacheKeys[2]);

        foreach ($cacheKeys as $key) {
            $this->app['cache']->forget('platform.page.enabled.'.$key);
        }
    }
}
