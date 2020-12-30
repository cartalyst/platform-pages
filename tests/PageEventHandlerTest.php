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
 * @version    10.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Pages\Tests;

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Pages\Handlers\EventHandler;

class PageEventHandlerTest extends IlluminateTestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->addToAssertionCount(1);

        m::close();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Handler
        $this->handler = new EventHandler($this->app);
    }

    /** @test */
    public function test_subscribe()
    {
        $class = get_class($this->handler);

        $this->app['events']->shouldReceive('listen')
            ->once()
            ->with('platform.page.creating', $class.'@creating')
        ;

        $this->app['events']->shouldReceive('listen')
            ->once()
            ->with('platform.page.created', $class.'@created')
        ;

        $this->app['events']->shouldReceive('listen')
            ->once()
            ->with('platform.page.updating', $class.'@updating')
        ;

        $this->app['events']->shouldReceive('listen')
            ->once()
            ->with('platform.page.updated', $class.'@updated')
        ;

        $this->app['events']->shouldReceive('listen')
            ->once()
            ->with('platform.page.deleted', $class.'@deleted')
        ;

        $this->handler->subscribe($this->app['events']);
    }

    /** @test */
    public function test_created()
    {
        $page = m::mock('Platform\Pages\Models\Page');

        $this->shouldFlushCache($page);

        $this->handler->created($page, []);
    }

    /** @test */
    public function test_updated()
    {
        $page = m::mock('Platform\Pages\Models\Page');

        $this->shouldFlushCache($page);

        $this->handler->updated($page, []);
    }

    /** @test */
    public function test_deleted()
    {
        $page = m::mock('Platform\Pages\Models\Page');

        $this->shouldFlushCache($page);

        $this->handler->deleted($page);
    }

    /**
     * Sets expected method calls for flushing cache.
     *
     * @param \Platform\Content\Models\Content $page
     *
     * @return void
     */
    protected function shouldFlushCache($page)
    {
        // Single page
        $this->app['cache']->shouldReceive('forget')
            ->once()
            ->with('platform.page.1')
        ;

        $this->app['cache']->shouldReceive('forget')
            ->once()
            ->with('platform.page.slug.foo')
        ;

        $this->app['cache']->shouldReceive('forget')
            ->once()
            ->with('platform.page.uri.foouri')
        ;

        // Enabled pages
        $this->app['cache']->shouldReceive('forget')
            ->once()
            ->with('platform.page.enabled.1')
        ;

        $this->app['cache']->shouldReceive('forget')
            ->once()
            ->with('platform.page.enabled.foo')
        ;

        $this->app['cache']->shouldReceive('forget')
            ->once()
            ->with('platform.page.enabled.foouri')
        ;

        // All pages
        $this->app['cache']->shouldReceive('forget')
            ->once()
            ->with('platform.pages.all')
        ;

        $this->app['cache']->shouldReceive('forget')
            ->once()
            ->with('platform.pages.all.enabled')
        ;

        $page->shouldReceive('getAttribute')
            ->once()
            ->with('id')
            ->andReturn(1)
        ;

        $page->shouldReceive('getAttribute')
            ->once()
            ->with('slug')
            ->andReturn('foo')
        ;

        $page->shouldReceive('getAttribute')
            ->once()
            ->with('uri')
            ->andReturn('foouri')
        ;
    }
}
