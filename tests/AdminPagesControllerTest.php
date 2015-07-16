<?php namespace Platform\Pages\Tests;
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
 * @version    1.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Platform\Menus\Models\Menu;
use Platform\Pages\Models\Page;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Pages\Controllers\Admin\PagesController;

class AdminPagesControllerTest extends IlluminateTestCase {

	/**
	 * Controller instance.
	 *
	 * @var \Platform\Pages\Controllers\Admin\PagesController
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

		// Admin Controller expectations
		$this->app['sentinel']->shouldReceive('getUser');
		$this->app['view']->shouldReceive('share');

		// Pages Repository
		$this->pages = m::mock('Platform\Pages\Repositories\PageRepositoryInterface');

		// Additional repositories
		$this->menus = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');
		$this->roles = m::mock('Platform\Users\Repositories\RoleRepositoryInterface');

		// Pages Controller
		$this->controller = new PagesController($this->pages);
	}

	/** @test */
	public function index_route()
	{
		$this->pages->shouldReceive('getAllTags')
			->once();

		$this->app['view']->shouldReceive('make')
			->once();

		$this->controller->index();
	}

	/** @test */
	public function create_route()
	{
		$this->app['view']->shouldReceive('make')
			->once();

		$this->pages->shouldReceive('getPreparedPage')
			->once()
			->andReturn(['page' => 'foo', 'roles' => null, 'templates' => null, 'files' => null, 'menus' => null, 'menu' => null]);

		$this->controller->create();
	}

	/** @test */
	public function edit_route()
	{
		$this->app['view']->shouldReceive('make')
			->once();

		$this->pages->shouldReceive('getPreparedPage')
			->once()
			->andReturn(['page' => 'foo', 'roles' => null, 'templates' => null, 'files' => null, 'menus' => null, 'menu' => null]);

		$this->controller->edit(1);
	}

	/** @test */
	public function edit_non_existing()
	{
		$this->pages->shouldReceive('getPreparedPage')
			->once();

		$this->app['alerts']->shouldReceive('error')
			->once();

		$this->app['translator']->shouldReceive('trans')
			->once();

		$this->app['redirect']->shouldReceive('toAdmin')
			->once()
			->andReturn($response = m::mock('Illuminate\Response\Response'));

		$this->pages->shouldReceive('find');

		$this->controller->edit(1);
	}

	/** @test */
	public function datagrid()
	{
		$this->app['datagrid']->shouldReceive('make')
			->once();

		$this->pages->shouldReceive('grid')
			->once();

		$this->controller->grid();
	}

	/** @test */
	public function store()
	{
		$this->app['alerts']->shouldReceive('success');

		$this->app['translator']->shouldReceive('trans');

		$this->app['request']->shouldReceive('all')
			->once()
			->andReturn(['slug' => 'foo']);

		$this->app['redirect']->shouldReceive('route')
			->once();

		$message = m::mock('Illuminate\Support\MessageBag');

		$message->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$this->pages->shouldReceive('store')
			->once()
			->andReturn([$message, $model = m::mock('Platform\Pages\Models\Page')]);

		$this->controller->store();
	}

	/** @test */
	public function update_route()
	{
		$this->app['alerts']->shouldReceive('success');

		$this->app['translator']->shouldReceive('trans');

		$this->app['request']->shouldReceive('all')
			->once()
			->andReturn(['slug' => 'foo']);

		$this->app['redirect']->shouldReceive('route')
			->once();

		$message = m::mock('Illuminate\Support\MessageBag');

		$message->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$this->pages->shouldReceive('store')
			->once()
			->andReturn([$message ,$model = m::mock('Platform\Pages\Models\Page')]);

		$this->controller->update(1);
	}

	/** @test */
	public function update_invalid_route()
	{
		$this->app['alerts']->shouldReceive('error');

		$this->app['translator']->shouldReceive('trans');

		$this->app['redirect']->shouldReceive('back')
			->once()
			->andReturn($this->app['redirect']);

		$this->app['redirect']->shouldReceive('withInput')
			->once()
			->andReturn($this->app['redirect']);

		$this->app['request']->shouldReceive('all')
			->once()
			->andReturn(['slug' => 'foo']);

		$message = m::mock('Illuminate\Support\MessageBag');

		$message->shouldReceive('isEmpty')
			->once()
			->andReturn(false);

		$this->pages->shouldReceive('store')
			->once()
			->andReturn([$message ,$model = m::mock('Platform\Pages\Models\Page')]);

		$this->controller->update(1);
	}

	/** @test */
	public function delete_route()
	{
		$this->app['alerts']->shouldReceive('success');

		$this->app['translator']->shouldReceive('trans');

		$this->app['redirect']->shouldReceive('route')
			->once();

		$this->pages->shouldReceive('delete')
			->once()
			->andReturn(true);

		$this->controller->delete(1);
	}

	/** @test */
	public function delete_not_existing_route()
	{
		$this->app['alerts']->shouldReceive('error');

		$this->app['translator']->shouldReceive('trans');

		$this->app['redirect']->shouldReceive('route')
			->once();

		$this->pages->shouldReceive('delete')
			->once();

		$this->controller->delete(1);
	}

	/** @test */
	public function copy_route()
	{
		$this->app['view']->shouldReceive('make')
			->once();

		$this->pages->shouldReceive('getPreparedPage')
			->once()
			->andReturn(['page' => 'foo', 'roles' => null, 'templates' => null, 'files' => null, 'menus' => null, 'menu' => null]);

		$this->controller->copy(1);
	}

	/** @test */
	public function execute_action()
	{
		$this->app['request']->shouldReceive('input')
			->with('action')
			->once()
			->andReturn('delete');

		$this->app['request']->shouldReceive('input')
			->with('rows', [])
			->once()
			->andReturn([1]);

		$this->pages->shouldReceive('delete')
			->with(1)
			->once();

		$this->assertContains('Success', (string) $this->controller->executeAction());
	}

	/** @test */
	public function execute_non_existing_action()
	{
		$this->app['request']->shouldReceive('input')
			->with('action')
			->once()
			->andReturn('foobar');

		$this->assertContains('Failed', (string) $this->controller->executeAction());
	}

}
