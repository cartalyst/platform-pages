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
 * @version    1.0.5
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Pages\Models\Page;

class PageModelTest extends IlluminateTestCase {

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
		parent::setUp();

		$this->page = new Page();
	}

	/** @test */
	public function it_has_a_type_accessor()
	{
		// Defaults to database
		$type = 'database';

		$this->assertSame($type, $this->page->type);
	}

	/** @test */
	public function it_has_a_roles_mutator_and_accessor()
	{
		$roles = [
			'foo' => 'bar',
		];

		$this->page->roles = $roles;

		$this->assertEquals(json_encode($roles), array_get($this->page->getAttributes(), 'roles'));

		$this->assertSame($roles, $this->page->roles);
	}

	/** @test */
	public function it_has_an_enabled_accessor()
	{
		// Defaults to true
		$enabled = true;

		$this->assertSame($enabled, $this->page->enabled);
	}

	/** @test */
	public function it_has_a_slug_mutator()
	{
		$slug = 'foo_bar';

		// Uses name if slug is not set
		$this->page->name = $slug;
		$this->page->slug = null;

		$this->assertEquals('foo-bar', $this->page->slug);

		// Turns underscores into dashes
		$this->page->slug = $slug;

		$this->assertEquals('foo-bar', $this->page->slug);
	}

	/** @test */
	public function it_has_a_uri_mutator()
	{
		$uri = 'foo ';

		$this->page->uri = $uri;

		$this->assertEquals('foo', $this->page->uri);
	}

	/** @test */
	public function it_has_a_template_accessor_and_mutator()
	{
		$defaultTemplate = 'foo';

		$this->app['config'] = m::mock('Illuminate\Config\Repository');
		$this->app['config']->shouldReceive('get')
			->with('platform/pages::default_template', '')
			->once()
			->andReturn($defaultTemplate);

		// Defaults to default_template config
		$this->assertEquals('foo', $this->page->template);

		// Set to null when type is filesystem
		$this->page->type     = 'filesystem';
		$this->page->template = $defaultTemplate;

		$this->assertNull(array_get($this->page->getAttributes(), 'template'));
	}

	/** @test */
	public function it_has_a_section_accessor_and_mutator()
	{
		$defaultSection = 'foo';

		$this->app['config'] = m::mock('Illuminate\Config\Repository');
		$this->app['config']->shouldReceive('get')
			->with('platform/pages::default_section', '')
			->once()
			->andReturn($defaultSection);

		// Defaults to default_section config
		$this->assertEquals('foo', $this->page->section);

		// Set to null when type is filesystem
		$this->page->type    = 'filesystem';
		$this->page->section = $defaultSection;

		$this->assertNull(array_get($this->page->getAttributes(), 'section'));
	}

	/** @test */
	public function it_has_a_value_mutator()
	{
		$value = 'foo';

		$this->page->type = 'filesystem';
		$this->page->value = $value;

		$this->assertNull($this->page->value);
	}

	/** @test */
	public function it_has_a_file_mutator()
	{
		$file = 'foo';

		$this->page->type = 'database';
		$this->page->file = $file;

		$this->assertNull($this->page->file);
	}

	/** @test */
	public function it_has_a_visibility_accessor()
	{
		$this->page->visibility = null;

		$this->assertEquals('always', $this->page->visibility);
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 */
	public function it_adds_rendering_callback()
	{
		$callback = function() {};

		$dispatcher = m::mock('Illuminate\Events\Dispatcher');
		$dispatcher->shouldReceive('listen')
			->with('platform.pages.rendering.*', $callback)
			->once();

		$this->page->setEventDispatcher($dispatcher);

		$this->page->rendering($callback);
	}

}
