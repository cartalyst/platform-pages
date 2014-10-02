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
use PHPUnit_Framework_TestCase;
use Illuminate\Support\Facades;
use Platform\Pages\Models\Page;
use Illuminate\Support\MessageBag;
use Illuminate\Container\Container;
use League\Fractal\Manager as Fractal;
use Illuminate\Database\Eloquent\Collection;
use Platform\Pages\Controllers\Api\V1\PagesController;

class ApiPagesControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * Controller instance.
	 *
	 * @var  \Platform\Pages\Controllers\Api\V1\PagesController
	 */
	protected $controller;

	/**
	 * Fractal manager instance.
	 *
	 * @var  \League\Fractal\Manager
	 */
	protected $fractal;

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
		$app = new Container;
		$app['request'] = m::mock('Illuminate\Http\Request');
		$app['request']->shouldReceive('input')
			->with('include', [])
			->once()
			->andReturn([]);

		Facades\Facade::setFacadeApplication($app);

		$this->fractal = new Fractal;
		$this->pages = m::mock('Platform\Pages\Repositories\PageRepositoryInterface');
		$this->controller = new PagesController($this->fractal, $this->pages);
	}

	/** @test */
	public function index_route()
	{
		$pageData = [
			'id' => 0,
			'name' => 'Foo',
			'slug' => 'foo',
			'created_at' => '',
		];

		$page = (new Page)->fill($pageData);

		$expected = [
			'data' => [
				$pageData,
			]
		];

		$this->pages->shouldReceive('findAll')
			->once()
			->andReturn(new Collection([$page]));

		$response = $this->controller->index();

		$this->assertSame($expected, $response->getData());
		$this->assertEquals(200, $response->getStatusCode());
	}

	/** @test */
	public function show_route()
	{
		$pageData = [
			'id' => 0,
			'name' => 'Foo',
			'slug' => 'foo',
			'created_at' => '',
		];

		$page = (new Page)->fill($pageData);

		$expected = [
			'data' => $pageData,
		];

		$this->pages->shouldReceive('find')
			->with(1)
			->once()
			->andReturn($page);

		$response = $this->controller->show(1);

		$this->assertSame($expected, $response->getData());
		$this->assertEquals(200, $response->getStatusCode());
	}

	/** @test */
	public function show_route_non_existing()
	{
		Facades\Lang::shouldReceive('get')
			->with('platform/pages::message.not_found', ['id' => 1])
			->once()
			->andReturn('Page [1] does not exist.');

		$pageData = [
			'id' => 0,
			'name' => 'Foo',
			'slug' => 'foo',
			'created_at' => '',
		];

		$this->pages->shouldReceive('find')
			->with(1)
			->once();

		$response = $this->controller->show(1);

		$this->assertError($response, 404);
	}

	/** @test */
	public function store_success()
	{
		Facades\Input::shouldReceive('json')
			->once()
			->andReturn($parameterBag = m::mock('Symfony\Component\HttpFoundation\ParameterBag'));

		$pageData = [
			'id' => 0,
			'name' => 'Foo',
			'slug' => 'foo',
			'created_at' => '',
		];

		$parameterBag->shouldReceive('all')
			->once()
			->andReturn($pageData);

		$this->pages->shouldReceive('validForCreation')
			->with($pageData)
			->once()
			->andReturn($messageBag = m::mock('Illuminate\Support\MessageBag'));

		$messageBag->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$page = (new Page)->fill($pageData);

		$this->pages->shouldReceive('create')
			->with($pageData)
			->once()
			->andReturn($page);

		$expected = [
			'data' => $pageData,
		];

		$response = $this->controller->store();

		$this->assertSame($expected, $response->getData());
		$this->assertEquals(200, $response->getStatusCode());
	}

	/** @test */
	public function store_failure()
	{
		Facades\Input::shouldReceive('json')
			->once()
			->andReturn($parameterBag = m::mock('Symfony\Component\HttpFoundation\ParameterBag'));

		$pageData = [
			'id' => 0,
			'name' => 'Foo',
			'slug' => 'foo',
			'created_at' => '',
		];

		$parameterBag->shouldReceive('all')
			->once()
			->andReturn($pageData);

		$this->pages->shouldReceive('validForCreation')
			->with($pageData)
			->once()
			->andReturn($messageBag = new MessageBag(['Error']));

		$response = $this->controller->store();

		$this->assertError($response, 422, $messageBag);
	}

	/** @test */
	public function update_success()
	{
		Facades\Input::shouldReceive('json')
			->once()
			->andReturn($parameterBag = m::mock('Symfony\Component\HttpFoundation\ParameterBag'));

		$pageData = [
			'id' => 0,
			'name' => 'Foo',
			'slug' => 'foo',
			'created_at' => '',
		];

		$page = (new Page)->fill($pageData);

		$parameterBag->shouldReceive('all')
			->once()
			->andReturn($pageData);

		$this->pages->shouldReceive('validForUpdate')
			->with(1, $pageData)
			->once()
			->andReturn($messageBag = m::mock('Illuminate\Support\MessageBag'));

		$messageBag->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$this->pages->shouldReceive('update')
			->with(1, $pageData)
			->once()
			->andReturn($page);

		$expected = [
			'data' => $pageData,
		];

		$response = $this->controller->update(1, $pageData);

		$this->assertSame($expected, $response->getData());
		$this->assertEquals(200, $response->getStatusCode());
	}

	/** @test */
	public function update_failure()
	{
		Facades\Input::shouldReceive('json')
			->once()
			->andReturn($parameterBag = m::mock('Symfony\Component\HttpFoundation\ParameterBag'));

		$pageData = [
			'id' => 0,
			'name' => 'Foo',
			'slug' => 'foo',
			'created_at' => '',
		];

		$parameterBag->shouldReceive('all')
			->once()
			->andReturn($pageData);

		$this->pages->shouldReceive('validForUpdate')
			->with(1, $pageData)
			->once()
			->andReturn($messageBag = new MessageBag(['error']));

		$response = $this->controller->update(1, $pageData);

		$this->assertError($response, 422, $messageBag);
	}

	/** @test */
	public function destroy_success()
	{
		$page = m::mock('Platform\Pages\Models\Page');

		$this->pages->shouldReceive('find')
			->with(1)
			->once()
			->andReturn($page);

		$page->shouldReceive('delete')
			->once();

		$response = $this->controller->destroy(1);

		$this->assertEquals(204, $response->getStatusCode());
		$this->assertNull($response->getData());
	}

	/** @test */
	public function destroy_failure()
	{
		Facades\Lang::shouldReceive('get')
			->with('platform/pages::message.not_found', ['id' => 1])
			->once()
			->andReturn('Page [1] does not exist.');

		$this->pages->shouldReceive('find')
			->with(1)
			->once();

		$response = $this->controller->destroy(1);

		$this->assertError($response, 404);
	}

	/**
	 * Asserts status code and errors.
	 *
	 * @param  \Cartalyst\Api\Response  $response
	 * @param  integer  $code
	 * @param  mixed   $errors
	 * @return void
	 */
	protected function assertError($response, $code = 404, $errors = ['Page [1] does not exist.'])
	{
		if ( ! is_array($errors))
		{
			$errors = (array) $errors;
		}

		$expected = [
			'errors' => $errors,
		];

		$this->assertEquals($code, $response->getStatusCode());
		$this->assertSame($expected, $response->getData());
	}

}
