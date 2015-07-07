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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Pages\Tests;

use Mockery as m;
use Platform\Menus\Models\Menu;
use Platform\Pages\Models\Page;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Pages\Controllers\Frontend\PagesController;

class FrontendPagesControllerTest extends IlluminateTestCase
{
    /**
     * Controller instance.
     *
     * @var \Platform\Pages\Controllers\Frontend\PagesController
     */
    protected $controller;

    /**
     * Setup.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // Base Controller expectations
        $this->app['sentinel']->shouldReceive('getUser')
            ->andReturn($this->user = m::mock('Cartalyst\Sentinel\Users\EloquentUser'));
        $this->app['view']->shouldReceive('share');

        // Additional bindings
        $this->app['router'] = m::mock('Illuminate\Routing\Router');

        // Pages Repository
        $this->pages = m::mock('Platform\Pages\Repositories\PageRepositoryInterface');

        // Additional repositories
        $this->menus = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');
        $this->roles = m::mock('Platform\Users\Repositories\RoleRepositoryInterface');

        // Set the router
        PagesController::setRouter($this->app['router']);

        // Pages Controller
        $this->controller = new PagesController($this->pages, $this->app['sentinel'], $this->app['router']);
    }

    /**
     * @test
     */
    public function render_page()
    {
        $this->app['router']->shouldReceive('current')
            ->once()
            ->andReturn($route = m::mock('Illuminate\Routing\Route'));

        $route->shouldReceive('getUri')
            ->once()
            ->andReturn('foo');

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('https')
            ->once();

        $this->pages->shouldReceive('render')
            ->with($model)
            ->once();

        $this->controller->page();
    }

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function do_not_render_admin_pages_for_other_users()
    {
        $this->app['router']->shouldReceive('current')
            ->andReturn($route = m::mock('Illuminate\Routing\Route'));

        $route->shouldReceive('getUri')
            ->andReturn('foo');

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->twice()
            ->andReturn('admin');

        $model->shouldReceive('getAttribute')
            ->with('https')
            ->once();

        $this->user->shouldReceive('getAttribute')
            ->with('roles')
            ->once()
            ->andReturn([]);

        $this->app['sentinel']->shouldReceive('hasAccess')
            ->with('superuser')
            ->once()
            ->andReturn(false);

        $this->controller->page();
    }

    /**
     * @test
     */
    public function render_pages_if_role_is_found()
    {
        $this->app['router']->shouldReceive('current')
            ->once()
            ->andReturn($route = m::mock('Illuminate\Routing\Route'));

        $route->shouldReceive('getUri')
            ->once()
            ->andReturn('foo');

        $this->app['request']
            ->shouldReceive('secure')
            ->once()
            ->andReturn(true);

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->twice()
            ->andReturn('logged_in');

        $this->app['sentinel']->shouldReceive('hasAccess')
            ->with('superuser')
            ->once()
            ->andReturn(false);

        $role = new \stdClass;
        $role->id = 'foo';

        $model->shouldReceive('hasGetMutator')
            ->with('roles')
            ->andReturn('getRolesAttribute');

        $model->shouldReceive('getAttributeValue')
            ->with('roles')
            ->once();

        $model->shouldReceive('getAttribute')
            ->atLeast()
            ->once()
            ->andReturn(['foo']);

        $this->pages->shouldReceive('render')
            ->with($model)
            ->once();

        $this->controller->page();
    }

    /**
     * @test
     */
    public function default_page()
    {
        $this->app['router']->shouldReceive('current')
            ->once()
            ->andReturn($route = m::mock('Illuminate\Routing\Route'));

        $route->shouldReceive('getUri')
            ->once()
            ->andReturn('/');

        $this->app['config']->shouldReceive('get')
            ->with('platform-pages.default_page', '')
            ->once()
            ->andReturn('foo');

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('https')
            ->once();

        $this->pages->shouldReceive('render')
            ->with($model)
            ->once();

        $this->controller->page();
    }

    /**
     * @test
     */
    public function redirect_on_secure()
    {
        $this->app['router']->shouldReceive('current')
            ->once()
            ->andReturn($route = m::mock('Illuminate\Routing\Route'));

        $route->shouldReceive('getUri')
            ->once()
            ->andReturn('foo');

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $model->shouldReceive('getAttribute')
            ->with('https')
            ->once()
            ->andReturn(true);

        $this->app['request']
            ->shouldReceive('secure')
            ->once()
            ->andReturn(false);

        $this->app['request']
            ->shouldReceive('getRequestUri')
            ->once();

        $this->app['redirect']->shouldReceive('secure')
            ->once()
            ->andReturn($this->app['redirect']);

        $this->controller->page();
    }

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function do_not_render_without_permissions()
    {
        $this->app['router']->shouldReceive('current')
            ->once()
            ->andReturn($route = m::mock('Illuminate\Routing\Route'));

        $route->shouldReceive('getUri')
            ->once()
            ->andReturn('foo');

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->once()
            ->andReturn('logged_in');

        $this->app['request']
            ->shouldReceive('secure')
            ->once()
            ->andReturn(true);

        $this->app['sentinel']->shouldReceive('hasAccess')
            ->with('superuser')
            ->once()
            ->andReturn(false);

        $this->user->shouldReceive('getAttribute')
            ->with('roles')
            ->once()
            ->andReturn([]);

        $model->shouldReceive('hasGetMutator')
            ->with('roles')
            ->andReturn('getRolesAttribute');

        $model->shouldReceive('getAttributeValue')
            ->with('roles')
            ->once()
            ->andReturn([]);

        $model->shouldReceive('getAttribute')
            ->atLeast()
            ->once()
            ->andReturn(['foobar']);

        $this->controller->page();
    }
}
