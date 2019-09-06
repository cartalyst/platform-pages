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

namespace Platform\Pages\Tests;

use Mockery as m;
use Illuminate\Support\Arr;
use Platform\Pages\Models\Page;
use Cartalyst\Testing\IlluminateTestCase;

class PageModelTest extends IlluminateTestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    protected function tearDown(): void
    {
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

        $this->assertSame(json_encode($roles), Arr::get($this->page->getAttributes(), 'roles'));

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

        $this->assertSame('foo-bar', $this->page->slug);

        // Turns underscores into dashes
        $this->page->slug = $slug;

        $this->assertSame('foo-bar', $this->page->slug);
    }

    /** @test */
    public function it_has_a_uri_mutator()
    {
        $uri = 'foo ';

        $this->page->uri = $uri;

        $this->assertSame('foo', $this->page->uri);
    }

    /** @test */
    public function it_has_a_template_accessor_and_mutator()
    {
        $defaultTemplate = 'foo';

        $this->app['config'] = m::mock('Illuminate\Config\Repository');
        $this->app['config']->shouldReceive('get')
            ->with('platform.pages.config.default_template', '')
            ->once()
            ->andReturn($defaultTemplate)
        ;

        // Defaults to default_template config
        $this->assertSame('foo', $this->page->template);

        // Set to null when type is filesystem
        $this->page->type     = 'filesystem';
        $this->page->template = $defaultTemplate;

        $this->assertNull(Arr::get($this->page->getAttributes(), 'template'));
    }

    /** @test */
    public function it_has_a_section_accessor_and_mutator()
    {
        $defaultSection = 'foo';

        $this->app['config'] = m::mock('Illuminate\Config\Repository');
        $this->app['config']->shouldReceive('get')
            ->with('platform.pages.config.default_section', '')
            ->once()
            ->andReturn($defaultSection)
        ;

        // Defaults to default_section config
        $this->assertSame('foo', $this->page->section);

        // Set to null when type is filesystem
        $this->page->type    = 'filesystem';
        $this->page->section = $defaultSection;

        $this->assertNull(Arr::get($this->page->getAttributes(), 'section'));
    }

    /** @test */
    public function it_has_a_value_mutator()
    {
        $value = 'foo';

        $this->page->type  = 'filesystem';
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

        $this->assertSame('always', $this->page->visibility);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_adds_rendering_callback()
    {
        $callback = function () {
        };

        $dispatcher = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $dispatcher->shouldReceive('listen')
            ->with('platform.pages.rendering.*', $callback)
            ->once()
        ;

        $this->page->setEventDispatcher($dispatcher);

        $this->page->rendering($callback);
    }
}
