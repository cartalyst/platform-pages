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

use Mockery as m;
use Platform\Menus\Models\Menu;
use Platform\Pages\Models\Page;
use PHPUnit_Framework_TestCase;
use Illuminate\Support\Facades;
use Cartalyst\DataGrid\Laravel\Facades\DataGrid;
use Platform\Pages\Controllers\Admin\PagesController;

class AdminPagesControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * Controller instance.
	 *
	 * @var  \Platform\Pages\Controllers\Admin\PagesController
	 */
	protected $controller;

	/**
	 * Menu repository instance.
	 *
	 * @var  \Platform\Menus\Repositories\MenuRepositoryInterface
	 */
	protected $menus;

	/**
	 * Role repository instance.
	 *
	 * @var  \Platform\Users\Repositories\RoleRepositoryInterface
	 */
	protected $roles;

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
		Facades\Config::shouldReceive('get')
			->atLeast()
			->once();

		$this->pages = m::mock('Platform\Pages\Repositories\PageRepositoryInterface');
		$this->menus = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');
		$this->roles = m::mock('Platform\Users\Repositories\RoleRepositoryInterface');

		$this->controller = new PagesController($this->pages, $this->menus, $this->roles);
	}

	/** @test */
	public function index_route()
	{
		Facades\View::shouldReceive('make')
			->once();

		$this->controller->index();
	}

	/** @test */
	public function create_route()
	{
		Facades\View::shouldReceive('make')
			->once();

		$this->menus->shouldReceive('findWhere')
			->with('page_id', 0)
			->once()
			->andReturn(new Menu);

		$this->menus->shouldReceive('findAllRoot')
			->once();

		$this->roles->shouldReceive('findAll')
			->once();

		$this->pages->shouldReceive('createModel')
			->once()
			->andReturn($model = new Page);

		$this->pages->shouldReceive('templates')
			->once()
			->andReturn([]);

		$this->pages->shouldReceive('files')
			->once()
			->andReturn([]);

		$this->controller->create();
	}

	/** @test */
	public function edit_route()
	{
		Facades\View::shouldReceive('make')
			->once();

		$this->menus->shouldReceive('findWhere')
			->with('page_id', 0)
			->once()
			->andReturn(new Menu);

		$this->menus->shouldReceive('findAllRoot')
			->once();

		$this->roles->shouldReceive('findAll')
			->once();

		$this->pages->shouldReceive('find')
			->once()
			->with(1)
			->andReturn(new Page);

		$this->pages->shouldReceive('templates')
			->once()
			->andReturn([]);

		$this->pages->shouldReceive('files')
			->once()
			->andReturn([]);

		$this->controller->edit(1);
	}

	/** @test */
	public function edit_non_existing()
	{
		Facades\View::shouldReceive('make')
			->once();

		Facades\Lang::shouldReceive('get')
			->once();

		Facades\Redirect::shouldReceive('toAdmin')
			->once()
			->andReturn($response = m::mock('Illuminate\Response\Response'));

		$response->shouldReceive('withErrors')
			->once();

		$this->pages->shouldReceive('find')
			->once()
			->with(1);

		$this->controller->edit(1);
	}

	/** @test */
	public function datagrid()
	{
		DataGrid::shouldReceive('make')
			->once();

		$this->pages->shouldReceive('grid')
			->once();

		$this->controller->grid();
	}

	/** @test */
	public function store()
	{
		$app = m::mock('Illuminate\Container\Container');
		$app->shouldReceive('offsetGet')
			->with('request')
			->twice()
			->andReturn($request = m::mock('Illuminate\Http\Request'));

		$request->shouldReceive('input')
			->once()
			->with('roles', [])
			->andReturn([]);

		$request->shouldReceive('merge')
			->with(['roles' => []])
			->once();

		$request->shouldReceive('all')
			->once()
			->andReturn([]);

		Facades\Input::setFacadeApplication($app);

		Facades\Redirect::shouldReceive('toAdmin')
			->once()
			->andReturn($response = m::mock('Illuminate\Response\Response'));

		$response->shouldReceive('withSuccess')
			->once();

		$this->pages->shouldReceive('validForCreation')
			->once()
			->andReturn($message = m::mock('Illuminate\Support\MessageBag'));

		$message->shouldReceive('isEmpty')
			->twice()
			->andReturn(true);

		$this->pages->shouldReceive('create')
			->once()
			->andReturn($model = m::mock('Platform\Pages\Models\Page'));

		$model->shouldReceive('getAttribute')
			->once();

		$this->controller->store();
	}

	/** @test */
	public function update_route()
	{
		Facades\Request::shouldReceive('all')
			->once()
			->andReturn(['slug' => 'foo']);

		Facades\Redirect::shouldReceive('toAdmin')
			->once()
			->andReturn($response = m::mock('Illuminate\Response\Response'));

		$response->shouldReceive('withSuccess')
			->once();

		$this->pages->shouldReceive('validForUpdate')
			->once()
			->andReturn($message = m::mock('Illuminate\Support\MessageBag'));

		$message->shouldReceive('isEmpty')
			->twice()
			->andReturn(true);

		$this->pages->shouldReceive('update')
			->once()
			->andReturn($model = m::mock('Platform\Pages\Models\Page'));

		$model->shouldReceive('getAttribute')
			->once();

		$this->controller->update(1);
	}

	/** @test */
	public function update_invalid_route()
	{
		Facades\Input::shouldReceive('all')
			->once()
			->andReturn(['slug' => 'foo']);

		Facades\Redirect::shouldReceive('back')
			->once()
			->andReturn($response = m::mock('Illuminate\Response\Response'));

		$response->shouldReceive('withInput')
			->once()
			->andReturn($response);

		$response->shouldReceive('withErrors')
			->once()
			->andReturn($response);

		$this->pages->shouldReceive('validForUpdate')
			->once()
			->andReturn($message = m::mock('Illuminate\Support\MessageBag'));

		$message->shouldReceive('isEmpty')
			->twice()
			->andReturn(false);

		$this->controller->update(1);
	}

	/** @test */
	public function delete_route()
	{
		Facades\Input::shouldReceive('all')
			->once()
			->andReturn(['slug' => 'foo']);

		Facades\Redirect::shouldReceive('toAdmin')
			->once()
			->andReturn($response = m::mock('Illuminate\Response\Response'));

		Facades\Lang::shouldReceive('get')
			->once();

		$response->shouldReceive('withSuccess')
			->once();

		$this->pages->shouldReceive('delete')
			->once()
			->andReturn($model = m::mock('Platform\Pages\Models\Page'));

		$this->controller->delete(1);
	}

	/** @test */
	public function delete_not_existing_route()
	{
		Facades\Input::shouldReceive('all')
			->once()
			->andReturn(['slug' => 'foo']);

		Facades\Redirect::shouldReceive('toAdmin')
			->once()
			->andReturn($response = m::mock('Illuminate\Response\Response'));

		Facades\Lang::shouldReceive('get')
			->once();

		$response->shouldReceive('withErrors')
			->once();

		$this->pages->shouldReceive('delete')
			->once();

		$this->controller->delete(1);
	}

	/** @test */
	public function copy_route()
	{
		Facades\View::shouldReceive('make')
			->once();

		$this->pages->shouldReceive('find')
			->once()
			->with(1)
			->andReturn($model = m::mock('Platform\Pages\Models\Page'));

		$this->pages->shouldReceive('files')
			->once()
			->andReturn([]);

		$this->menus->shouldReceive('findWhere')
			->with('page_id', 0)
			->once();

		$this->menus->shouldReceive('createModel')
			->once()
			->andReturn(new Menu);

		$this->menus->shouldReceive('findAllRoot')
			->once();

		$this->roles->shouldReceive('findAll')
			->once();

		$model->shouldReceive('getAttribute')
			->once();

		$this->pages->shouldReceive('templates')
			->once()
			->andReturn([]);

		$this->controller->copy(1);
	}

	/** @test */
	public function execute_action()
	{
		$app = m::mock('Illuminate\Container\Container');
		$app->shouldReceive('offsetGet')
			->with('request')
			->twice()
			->andReturn($request = m::mock('Illuminate\Http\Request'));

		$request->shouldReceive('input')
			->once()
			->with('action', '')
			->andReturn('enable');

		$request->shouldReceive('input')
			->once()
			->with('entries', [])
			->andReturn([1]);

		Facades\Input::setFacadeApplication($app);

		$this->pages->shouldReceive('enable')
			->once()
			->with(1);

		$this->controller->executeAction();
	}

	/** @test */
	public function execute_non_existing_action()
	{
		$app = m::mock('Illuminate\Container\Container');
		$app->shouldReceive('offsetGet')
			->with('request')
			->once()
			->andReturn($request = m::mock('Illuminate\Http\Request'));

		$request->shouldReceive('input')
			->once()
			->with('action', '')
			->andReturn('foo');

		Facades\Input::setFacadeApplication($app);

		$this->controller->executeAction();
	}

}
