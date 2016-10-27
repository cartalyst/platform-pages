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
 * @version    4.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Pages\Tests;

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Pages\Menus\PageType;

class PageTypeMenuTest extends IlluminateTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Additional IoC bindings
        $this->app['platform.pages'] = m::mock('Platform\Pages\Repositories\PageRepositoryInterface');

        // Repository
        $this->type = new PageType($this->app);
    }

    /** @test */
    public function it_hasn_identifier()
    {
        $this->assertEquals('page', $this->type->getIdentifier());
    }

    /** @test */
    public function it_has_a_name()
    {
        $this->assertEquals('Page', $this->type->getName());
    }

    /** @test */
    public function it_can_retrieve_the_form_html()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $this->app['platform.pages']->shouldReceive('findAll')
            ->once()
            ->andReturn([]);

        $this->app['view']->shouldReceive('make')
            ->with('platform/pages::types/form', ['child' => $menu, 'pages'=> []])
            ->once();

        $this->type->getFormHtml($menu);
    }

    /** @test */
    public function it_can_retrieve_the_template_html()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $this->app['platform.pages']->shouldReceive('findAll')
            ->once()
            ->andReturn([]);

        $this->app['view']->shouldReceive('make')
            ->with('platform/pages::types/template', ['pages'=> []])
            ->once();

        $this->type->getTemplateHtml($menu);
    }

    /** @test */
    public function it_can_set_uri_and_page_id_after_save()
    {
        $page = m::mock('Platform\Pages\Models\Page');
        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('getAttributes')
            ->once()
            ->andReturn([
                'page_id' => 1,
            ]);

        $page->shouldReceive('getAttribute')
            ->with('uri')
            ->once()
            ->andReturn($pageUri = 'foo');

        $menu->shouldReceive('setAttribute')
            ->with('uri', $pageUri)
            ->once();

        $menu->shouldReceive('setAttribute')
            ->with('page_id', 1)
            ->once();

        $this->app['platform.pages']->shouldReceive('find')
            ->once()
            ->andReturn($page);

        $this->type->afterSave($menu);
    }

    /** @test */
    public function it_caches_pages_after_fetching_and_sets_uri_to_null_on_root()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');
        $page = m::mock('Platform\Pages\Models\Page');

        $page->shouldReceive('getAttribute')
            ->with('uri')
            ->once()
            ->andReturn('/');

        $page->shouldReceive('setAttribute')
            ->with('uri', '')
            ->once();

        $this->app['platform.pages']->shouldReceive('findAll')
            ->once()
            ->andReturn([$page]);

        $this->app['view']->shouldReceive('make')
            ->with('platform/pages::types/form', ['child' => $menu, 'pages'=> [$page]])
            ->once();

        $this->type->getFormHtml($menu);

        $this->app['view']->shouldReceive('make')
            ->with('platform/pages::types/template', ['pages'=> [$page]])
            ->once();

        $this->type->getTemplateHtml($menu);
    }
}
