<?php namespace Platform\Pages\Tests;
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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use StdClass;
use Mockery as m;
use PHPUnit_Framework_TestCase;
use Illuminate\Support\Facades;
use Platform\Pages\Controllers\Frontend\PagesController;

class FrontendPagesControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * App instance.
	 *
	 * @var  \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * Controller instance.
	 *
	 * @var  \Platform\Pages\Controllers\Frontend\PagesController
	 */
	protected $controller;

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->app = m::mock('Illuminate\Container\Container');
		$this->pages = m::mock('Platform\Pages\Repositories\PageRepositoryInterface');
		$menus = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');
		$roles = m::mock('Platform\Users\Repositories\RoleRepositoryInterface');

		$this->controller = new PagesController($this->pages, $menus, $roles);

		$router = m::mock('Illuminate\Routing\Router');

		$this->app->shouldReceive('offsetGet')
			->with('router')
			->andReturn($router);

		$router->shouldReceive('current')
			->andReturn($route = m::mock('Illuminate\Routing\Route'));

		$route->shouldReceive('getUri')
			->andReturn('foo');

		Facades\Facade::clearResolvedInstances();
		Facades\Facade::setFacadeApplication($this->app);
	}

	/**
	 * @test
	 */
	public function render_page()
	{
		$this->pages->shouldReceive('findEnabled')
			->with('foo')
			->once()
			->andReturn($model = m::mock('Platform\Pages\Models\Page'));

		$model->shouldReceive('getAttribute')
			->with('visibility')
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
		$this->pages->shouldReceive('findEnabled')
			->with('foo')
			->once()
			->andReturn($model = m::mock('Platform\Pages\Models\Page'));

		$model->shouldReceive('getAttribute')
			->with('visibility')
			->twice()
			->andReturn('admin');

		$this->app->shouldReceive('offsetGet')
			->with('sentinel')
			->once()
			->andReturn($sentinel = m::mock('Cartalyst\Sentinel\Sentinel'));

		$sentinel->shouldReceive('getUser')
			->once()
			->andReturn($user = m::mock('Cartalyst\Sentinel\Users\EloquentUser'));

		$sentinel->shouldReceive('hasAccess')
			->with('admin')
			->once()
			->andReturn(false);

		$this->controller->page();
	}

	/**
	 * @test
	 */
	public function render_pages_if_role_is_found()
	{
		$this->pages->shouldReceive('findEnabled')
			->with('foo')
			->once()
			->andReturn($model = m::mock('Platform\Pages\Models\Page'));

		$model->shouldReceive('getAttribute')
			->with('visibility')
			->twice()
			->andReturn('logged_in');

		$this->app->shouldReceive('offsetGet')
			->with('sentinel')
			->once()
			->andReturn($sentinel = m::mock('Cartalyst\Sentinel\Sentinel'));

		$sentinel->shouldReceive('getUser')
			->once()
			->andReturn($user = m::mock('Cartalyst\Sentinel\Users\EloquentUser'));

		$sentinel->shouldReceive('hasAccess')
			->with('admin')
			->once()
			->andReturn(false);

		$role = new StdClass;
		$role->id = 'foo';

		$user->shouldReceive('getAttribute')
			->with('roles')
			->once()
			->andReturn([$role]);

		$model->shouldReceive('hasGetMutator')
			->with('roles')
			->twice()
			->andReturn(true);

		$model->shouldReceive('getRolesAttribute')
			->once()
			->andReturn(['foo']);

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
	 * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
	 */
	public function not_existing_page()
	{
		$this->pages->shouldReceive('findEnabled')
			->with('foo')
			->once();

		$this->controller->page();
	}

	/**
	 * @test
	 * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
	 */
	public function default_page()
	{
		$this->app = m::mock('Illuminate\Container\Container');

		$router = m::mock('Illuminate\Routing\Router');

		$this->app->shouldReceive('offsetGet')
			->with('router')
			->andReturn($router);

		$router->shouldReceive('current')
			->andReturn($route = m::mock('Illuminate\Routing\Route'));

		$route->shouldReceive('getUri')
			->andReturn('/');

		Facades\Facade::setFacadeApplication($this->app);

		$this->app->shouldReceive('offsetGet')
			->with('config')
			->andReturn($config = m::mock('Illuminate\Config\Repository'));

		$config->shouldReceive('get')
			->with('platform/pages::default_page')
			->once()
			->andReturn('foo');

		$this->pages->shouldReceive('findEnabled')
			->with('foo')
			->once();

		$this->controller->page();
	}

	/**
	 * @test
	 * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
	 */
	public function do_not_render_without_permissions()
	{
		$this->pages->shouldReceive('findEnabled')
			->with('foo')
			->once()
			->andReturn($model = m::mock('Platform\Pages\Models\Page'));

		$model->shouldReceive('getAttribute')
			->with('visibility')
			->once()
			->andReturn('logged_in');

		$this->app->shouldReceive('offsetGet')
			->with('sentinel')
			->once()
			->andReturn($sentinel = m::mock('Cartalyst\Sentinel\Sentinel'));

		$sentinel->shouldReceive('getUser')
			->once()
			->andReturn($user = m::mock('Cartalyst\Sentinel\Users\EloquentUser'));

		$sentinel->shouldReceive('hasAccess')
			->with('admin')
			->once()
			->andReturn(false);

		$user->shouldReceive('getAttribute')
			->with('roles')
			->once()
			->andReturn([]);

		$model->shouldReceive('hasGetMutator')
			->with('roles')
			->twice()
			->andReturn(true);

		$model->shouldReceive('getRolesAttribute')
			->once()
			->andReturn(['foo']);

		$model->shouldReceive('getAttribute')
			->atLeast()
			->once()
			->andReturn(['foobar']);

		$this->controller->page();
	}

}
