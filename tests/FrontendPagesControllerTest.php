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
 * @version    8.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Pages\Tests;

use Mockery as m;
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
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Base Controller expectations
        $this->app['sentinel']->shouldReceive('getUser')
            ->andReturn($this->user = m::mock('Cartalyst\Sentinel\Users\EloquentUser'))
        ;
        $this->app['Illuminate\Contracts\View\Factory']->shouldReceive('share');

        // Additional bindings
        $this->app['router'] = m::mock('Illuminate\Routing\Router');

        // Pages Repository
        $this->pages = m::mock('Platform\Pages\Repositories\PageRepositoryInterface');

        // Additional repositories
        $this->menus = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');
        $this->roles = m::mock('Platform\Users\Repositories\RoleRepositoryInterface');

        // Pages Controller
        $this->controller = new PagesController($this->pages, $this->app['sentinel'], $this->app['router']);
    }

    /**
     * @test
     */
    public function render_page()
    {
        $this->app['request']->shouldReceive('path')
            ->once()
            ->andReturn('foo')
        ;

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'))
        ;

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->once()
        ;

        $model->shouldReceive('getAttribute')
            ->with('https')
            ->once()
        ;

        $this->pages->shouldReceive('render')
            ->with($model)
            ->once()
        ;

        $this->controller->page($this->app['request']);
    }

    /**
     * @test
     */
    public function do_not_render_admin_pages_for_other_users()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\HttpException');

        $this->app['request']->shouldReceive('path')
            ->once()
            ->andReturn('foo')
        ;

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'))
        ;

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->twice()
            ->andReturn('admin')
        ;

        $model->shouldReceive('getAttribute')
            ->with('https')
            ->once()
        ;

        $this->user->shouldReceive('getAttribute')
            ->with('roles')
            ->once()
            ->andReturn([])
        ;

        $this->app['sentinel']->shouldReceive('hasAccess')
            ->with('superuser')
            ->once()
            ->andReturn(false)
        ;

        $this->controller->page($this->app['request']);
    }

    /**
     * @test
     */
    public function render_pages_if_role_is_found()
    {
        $this->app['request']->shouldReceive('path')
            ->once()
            ->andReturn('foo')
        ;

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'))
        ;

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->twice()
            ->andReturn('logged_in')
        ;

        $model->shouldReceive('getAttribute')
            ->with('https')
            ->once()
        ;

        $model->shouldReceive('offsetExists')
            ->with('roles')
            ->once()
            ->andReturn(true)
        ;

        $this->app['sentinel']->shouldReceive('hasAccess')
            ->with('superuser')
            ->once()
            ->andReturn(false)
        ;

        $role     = new \stdClass();
        $role->id = 'foo';

        $this->user->shouldReceive('getAttribute')
            ->with('roles')
            ->once()
            ->andReturn([$role])
        ;

        $model->shouldReceive('getAttribute')
            ->with('roles')
            ->times(2)
            ->andReturn(['foo'])
        ;

        $this->pages->shouldReceive('render')
            ->with($model)
            ->once()
        ;

        $this->controller->page($this->app['request']);
    }

    /**
     * @test
     */
    public function default_page()
    {
        $this->app['request']->shouldReceive('path')
            ->once()
            ->andReturn('/')
        ;

        $this->app['config']->shouldReceive('get')
            ->with('platform.pages.config.default_page', '')
            ->once()
            ->andReturn('foo')
        ;

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'))
        ;

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->once()
        ;

        $model->shouldReceive('getAttribute')
            ->with('https')
            ->once()
        ;

        $this->pages->shouldReceive('render')
            ->with($model)
            ->once()
        ;

        $this->controller->page($this->app['request']);
    }

    /**
     * @test
     */
    public function redirect_on_secure()
    {
        $this->app['request']->shouldReceive('path')
            ->once()
            ->andReturn('foo')
        ;

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'))
        ;

        $model->shouldReceive('getAttribute')
            ->with('https')
            ->once()
            ->andReturn(true)
        ;

        $this->app['request']
            ->shouldReceive('secure')
            ->once()
            ->andReturn(false)
        ;

        $this->app['request']
            ->shouldReceive('getRequestUri')
            ->once()
        ;

        $this->app['redirect']->shouldReceive('secure')
            ->once()
            ->andReturn($this->app['redirect'])
        ;

        $this->controller->page($this->app['request']);
    }

    /**
     * @test
     */
    public function do_not_render_without_permissions()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\HttpException');

        $this->app['request']->shouldReceive('path')
            ->once()
            ->andReturn('foo')
        ;

        $this->pages->shouldReceive('findEnabled')
            ->with('foo')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'))
        ;

        $model->shouldReceive('getAttribute')
            ->with('visibility')
            ->once()
            ->andReturn('logged_in')
        ;

        $model->shouldReceive('offsetExists')
            ->with('roles')
            ->twice()
            ->andReturn(false)
        ;

        $this->app['request']
            ->shouldReceive('secure')
            ->once()
            ->andReturn(true)
        ;

        $this->app['sentinel']->shouldReceive('hasAccess')
            ->with('superuser')
            ->once()
            ->andReturn(false)
        ;

        $model->shouldReceive('hasGetMutator')
            ->with('roles')
            ->andReturn('getRolesAttribute')
        ;

        $model->shouldReceive('getAttribute')
            ->atLeast()
            ->once()
            ->andReturn(['foobar'])
        ;

        $this->controller->page($this->app['request']);
    }
}
