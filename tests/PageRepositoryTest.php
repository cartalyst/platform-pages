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
 * @version    5.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Pages\Tests;

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;

class PageRepositoryTest extends IlluminateTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Additional Bindings
        $this->app['platform.content']            = m::mock('Platform\Content\Repositories\ContentRepositoryInterface');
        $this->app['platform.menus']              = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');
        $this->app['platform.menus.manager']      = m::mock('Platform\Menus\Repositories\ManagerRepositoryInterface');
        $this->app['platform.pages.handler.data'] = m::mock('Platform\Pages\Handlers\DataHandlerInterface');
        $this->app['platform.pages.manager']      = m::mock('Platform\Pages\Repositories\ManagerRepository');
        $this->app['platform.pages.validator']    = m::mock('Cartalyst\Support\Validator');
        $this->app['platform.permissions']        = m::mock('Platform\Permissions\Repositories\PermissionsRepositoryInterface');
        $this->app['platform.roles']              = m::mock('Platform\Roles\Repositories\RoleRepositoryInterface');
        $this->app['platform.tags']               = m::mock('Platform\Tags\Repositories\TagsRepositoryInterface');
        $this->app['themes']                      = m::mock('Cartalyst\Themes\ThemeBag');

        $this->app['platform.menus.manager']->shouldIgnoreMissing();

        // Repository
        $this->repository = m::mock('Platform\Pages\Repositories\PageRepository[createModel]', [$this->app]);
    }

    /** @test */
    public function it_can_generate_the_grid()
    {
        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model = m::mock('Illuminate\Database\Eloquent\Model'));

        $this->repository->grid();
    }

    /** @test */
    public function it_can_find_all()
    {
        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $model->shouldReceive('get')
            ->once()
            ->andReturn($collection = m::mock('Illuminate\Database\Eloquent\Collection'));

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.pages.all', m::on(function ($callback) {
                $callback();
                return true;
            }))->andReturn($collection);

        $pages = $this->repository->findAll();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $pages);
    }

    /** @test */
    public function it_can_find_all_enabled()
    {
        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $model->shouldReceive('whereEnabled')
            ->with(1)
            ->once()
            ->andReturn($model);

        $model->shouldReceive('get')
            ->once()
            ->andReturn($collection = m::mock('Illuminate\Database\Eloquent\Collection'));

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.pages.all.enabled', m::on(function ($callback) {
                $callback();
                return true;
            }))->andReturn($collection);

        $pages = $this->repository->findAllEnabled();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $pages);
    }

    /** @test */
    public function it_can_find_records_by_id()
    {
        $model = $this->shouldReceiveFind();

        $this->repository->find(1);
    }

    /** @test */
    public function it_can_find_records_by_slug_or_uri()
    {
        $model = m::mock('Platform\Pages\Models\Page');

        $this->repository->shouldReceive('createModel')
            ->twice()
            ->andReturn($model);

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.page.slug.foo', m::on(function ($callback) {
                $callback();
                return true;
            }));

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.page.uri.foo', m::on(function ($callback) {
                $callback();
                return true;
            }))
            ->andReturn($model);

        $model->shouldReceive('whereSlug')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('whereUri')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('first')
            ->once();

        $model->shouldReceive('first')
            ->once()
            ->andReturn($model);

        $this->repository->find('foo');
    }

    /** @test */
    public function it_can_find_enabled_records()
    {
        $model = m::mock('Platform\Pages\Models\Page');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.page.enabled.1', m::on(function ($callback) {
                $callback();
                return true;
            }));

        $model->shouldReceive('where')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('whereNested')
            ->with(m::on(function ($callback) use ($model) {
                $model->shouldReceive('orWhere')
                    ->with('slug', 1)
                    ->once()
                    ->andReturn($model);

                $model->shouldReceive('orWhere')
                    ->with('uri', 1)
                    ->once()
                    ->andReturn($model);

                $model->shouldReceive('orWhere')
                    ->with('id', 1)
                    ->once()
                    ->andReturn($model);

                $callback($model);
                return true;
            }))
            ->once()
            ->andReturn($model);

        $model->shouldReceive('first')
            ->once()
            ->andReturn($model);

        $this->repository->findEnabled(1);
    }

    /** @test */
    public function it_can_retrieve_prepared_pages()
    {
        $this->app['themes']->shouldIgnoreMissing([]);

        $menu = m::mock('Platform\Menus\Models\Menu');

        $model = $this->shouldReceiveCreateModel();

        $model->shouldReceive('getAttribute')
            ->with('id')
            ->once()
            ->andReturn(1);

        $this->app['platform.menus']->shouldReceive('findWhere')
            ->with('page_id', 1)
            ->once();

        $this->app['platform.menus']->shouldReceive('findAllRoot')
            ->once();

        $this->app['platform.menus']->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $this->app['platform.roles']->shouldReceive('findAll')
            ->once()
            ->andReturn([]);

        $this->app['view']->shouldReceive('getExtensions')
            ->once()
            ->andReturn([]);

        $preparedPage = $this->repository->getPreparedPage(null);

        $this->assertArrayHasKey('page', $preparedPage);
        $this->assertArrayHasKey('files', $preparedPage);
        $this->assertArrayHasKey('roles', $preparedPage);
        $this->assertArrayHasKey('templates', $preparedPage);
        $this->assertArrayHasKey('menus', $preparedPage);
        $this->assertArrayHasKey('menu', $preparedPage);
    }

    /** @test */
    public function it_create_a_new_model_if_the_passed_id_cannot_be_found()
    {
        $this->app['themes']->shouldIgnoreMissing([]);

        $menu = m::mock('Platform\Menus\Models\Menu');

        $model = $this->shouldReceiveFind();
        $model->exists = true;

        $model->shouldReceive('getAttribute')
            ->with('id')
            ->once()
            ->andReturn(1);

        $this->app['platform.menus']->shouldReceive('findWhere')
            ->with('page_id', 1)
            ->once();

        $this->app['platform.menus']->shouldReceive('findAllRoot')
            ->once();

        $this->app['platform.menus']->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $this->app['platform.roles']->shouldReceive('findAll')
            ->once()
            ->andReturn([]);

        $this->app['view']->shouldReceive('getExtensions')
            ->once()
            ->andReturn([]);

        $preparedPage = $this->repository->getPreparedPage(1);

        $this->assertArrayHasKey('page', $preparedPage);
        $this->assertArrayHasKey('files', $preparedPage);
        $this->assertArrayHasKey('roles', $preparedPage);
        $this->assertArrayHasKey('templates', $preparedPage);
        $this->assertArrayHasKey('menus', $preparedPage);
        $this->assertArrayHasKey('menu', $preparedPage);
    }

    /** @test */
    public function it_returs_false_if_no_pages_found_for_preparation()
    {
        $model = $this->shouldReceiveFind(null, false);

        $preparedPage = $this->repository->getPreparedPage(1);

        $this->assertFalse($preparedPage);
    }

    /** @test */
    public function it_can_validate_for_creation()
    {
        $data = ['slug' => 'foo'];

        $this->app['platform.pages.validator']->shouldReceive('on')
            ->with('create')
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('validate')
            ->once()
            ->andReturn(true);

        $this->repository->setValidator($this->app['platform.pages.validator']);

        $this->assertTrue($this->repository->validForCreation($data));
    }

    /** @test */
    public function it_can_validate_for_update()
    {
        $data = ['slug' => 'foo', 'uri' => 'foo'];

        $model = m::mock('Platform\Pages\Models\Page');

        $this->app['platform.pages.validator']->shouldReceive('on')
            ->with('update')
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('bind')
            ->with($data)
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('validate')
            ->once()
            ->andReturn(true);

        $model->shouldReceive('getAttribute')
            ->once()
            ->with('slug')
            ->andReturn('foo');

        $model->shouldReceive('getAttribute')
            ->once()
            ->with('uri')
            ->andReturn('foo');

        $this->assertTrue($this->repository->validForUpdate($model, $data));
    }

    /** @test */
    public function it_can_store()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('where')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('first')
            ->once();

        $tags = ['foo', 'bar'];

        $this->app['platform.tags']->shouldReceive('set')
            ->with(m::any(), $tags)
            ->once();

        $this->app['platform.menus']->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $data = ['slug' => 'foo', 'tags' => $tags];

        $this->shouldReceiveCreate($data);

        $this->repository->store(null, $data);
    }

    /** @test */
    public function it_can_create()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('where')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('first')
            ->once();

        $tags = ['foo', 'bar'];

        $this->app['platform.tags']->shouldReceive('set')
            ->with(m::any(), $tags)
            ->once();

        $this->app['platform.menus']->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $data = ['slug' => 'foo', 'tags' => $tags];

        $this->shouldReceiveCreate($data);

        list($messages, $page) = $this->repository->create($data);

        $this->assertInstanceOf('Platform\Pages\Models\Page', $page);
    }

    /** @test */
    public function it_can_create_with_menu()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $tags = ['foo', 'bar'];

        $this->app['platform.tags']->shouldReceive('set')
            ->with(m::any(), $tags)
            ->once();

        $this->app['platform.menus']->shouldReceive('createModel')
            ->twice()
            ->andReturn($menu);

        $menu->shouldReceive('whereMenu')
            ->with(1)
            ->twice()
            ->andReturn($menu);

        $menu->shouldReceive('where')
            ->with('page_id', 1)
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('first')
            ->times(3)
            ->andReturn($menu);

        $menu->shouldReceive('getAttribute')
            ->with('menu')
            ->once()
            ->andReturn(2);

        $menu->shouldReceive('getAttribute')
            ->with('slug')
            ->once()
            ->andReturn('foo');

        $menu->shouldReceive('getGuarded')
            ->once()
            ->andReturn([]);

        $menu->shouldReceive('getAttributes')
            ->once()
            ->andReturn([]);

        $menu->shouldReceive('delete')
            ->once();

        $menu->shouldReceive('makeLastChildOf')
            ->once();

        $menu->shouldReceive('fill')
                ->once()
                ->andReturn($menu);

        $menu->shouldReceive('save')
                ->once();

        $data = ['slug' => 'foo', 'menu' => 1, 'tags' => $tags];

        $model = $this->shouldReceiveCreate($data);

        $model->shouldReceive('getAttribute')
            ->with('id')
            ->once()
            ->andReturn(1);

        $model->shouldReceive('getAttribute');

        list($messages, $page) = $this->repository->create($data);

        $this->assertInstanceOf('Platform\Pages\Models\Page', $page);
    }

    /** @test */
    public function it_will_stop_creating_if_event_returns_false()
    {
        $data = ['slug' => 'foo'];

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.creating', [ $data ])
            ->once()
            ->andReturn(false);

        list($messages, $page) = $this->repository->create($data);

        $this->assertNull($page);
    }

    /** @test */
    public function it_will_stop_updating_if_event_returns_false()
    {
        $data = ['slug' => 'foo'];

        $model = $this->shouldReceiveFind();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.updating', [ $model, $data ])
            ->once()
            ->andReturn(false);

        list($messages, $page) = $this->repository->update(1, $data);

        $this->assertNull($page);
    }

    /** @test */
    public function it_can_update_existing_records()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('where')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('first')
            ->once();

        $tags = ['foo', 'bar'];

        $this->app['platform.tags']->shouldReceive('set')
            ->with(m::any(), $tags)
            ->once();

        $this->app['platform.menus']->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $data = ['slug' => 'foo', 'uri' => 'foo', 'tags' => $tags];

        $this->shouldReceiveUpdate($data);

        list($messages, $page) = $this->repository->update(1, $data);

        $this->assertInstanceOf('Platform\Pages\Models\Page', $page);
    }

    /** @test */
    public function it_can_delete_existing_records()
    {
        $model = $this->shouldReceiveFind();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.deleted', [ $model ])
            ->once();

        $model->shouldReceive('delete')
            ->once();

        $this->assertTrue($this->repository->delete(1));
    }

    /** @test */
    public function it_will_return_false_when_trying_to_delete_non_existing_records()
    {
        $model = $this->shouldReceiveFind(null, false);

        $this->assertFalse($this->repository->delete(1));
    }

    /** @test */
    public function it_can_render_pages()
    {
        $this->app['themes']->shouldIgnoreMissing($bag = m::mock('Cartalyst\Themes\ThemeBag'));

        $bag->shouldReceive('inject')
            ->once();

        $bag->shouldReceive('render')
            ->once();

        $model = m::mock('Platform\Pages\Models\Page');

        $model->shouldReceive('getAttribute')
            ->with('type')
            ->once()
            ->andReturn('database');

        $model->shouldReceive('getAttribute')
            ->with('template')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('slug')
            ->once()
            ->andReturn('foo');

        $model->shouldReceive('getAttribute')
            ->with('value')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('section')
            ->once();

        $model->shouldReceive('getEventDispatcher')
            ->once()
            ->andReturn($this->app['events']);

        $this->app['events']->shouldReceive('fire')
            ->with('platform.pages.rendering.foo', ['page' => $model])
            ->once()
            ->andReturn([['foo' => 'bar']]);

        $this->app['platform.content']->shouldReceive('prepareForRendering')
            ->once();

        $this->repository->render($model);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_if_rendering_listener_returns_incompatible_data()
    {
        $this->app['themes']->shouldIgnoreMissing($bag = m::mock('Cartalyst\Themes\ThemeBag'));

        $bag->shouldReceive('inject')
            ->once();

        $model = m::mock('Platform\Pages\Models\Page');

        $model->shouldReceive('getAttribute')
            ->with('type')
            ->once()
            ->andReturn('database');

        $model->shouldReceive('getAttribute')
            ->with('template')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('slug')
            ->once()
            ->andReturn('foo');

        $model->shouldReceive('getAttribute')
            ->with('value')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('section')
            ->once();

        $model->shouldReceive('getEventDispatcher')
            ->once()
            ->andReturn($this->app['events']);

        $this->app['events']->shouldReceive('fire')
            ->with('platform.pages.rendering.foo', ['page' => $model])
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->app['platform.content']->shouldReceive('prepareForRendering')
            ->once();

        $this->repository->render($model);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_throws_an_exception_if_rendering_listener_returns_a_page_key()
    {
        $this->app['themes']->shouldIgnoreMissing($bag = m::mock('Cartalyst\Themes\ThemeBag'));

        $bag->shouldReceive('inject')
            ->once();

        $model = m::mock('Platform\Pages\Models\Page');

        $model->shouldReceive('getAttribute')
            ->with('type')
            ->once()
            ->andReturn('database');

        $model->shouldReceive('getAttribute')
            ->with('template')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('slug')
            ->once()
            ->andReturn('foo');

        $model->shouldReceive('getAttribute')
            ->with('value')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('section')
            ->once();

        $model->shouldReceive('getEventDispatcher')
            ->once()
            ->andReturn($this->app['events']);

        $this->app['events']->shouldReceive('fire')
            ->with('platform.pages.rendering.foo', ['page' => $model])
            ->once()
            ->andReturn([['page' => 'bar']]);

        $this->app['platform.content']->shouldReceive('prepareForRendering')
            ->once();

        $this->repository->render($model);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_throws_an_exception_when_rendering_an_invalid_type()
    {
        $model = m::mock('Platform\Pages\Models\Page');

        $model->shouldReceive('getAttribute')
            ->with('type')
            ->once();

        $model->shouldReceive('getKey')
            ->once();

        $this->repository->render($model);
    }

    /** @test */
    public function it_can_enable_pages()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('where')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('first')
            ->once();

        $this->app['platform.tags']->shouldReceive('set')
            ->once();

        $this->app['platform.menus']->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $data = ['enabled' => 1];

        $model = $this->shouldReceiveFind();

        $model->shouldReceive('getAttribute')
            ->with('id')
            ->once();

        $this->app['platform.pages.validator']->shouldReceive('bypass')
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.updating', [ $model, $data ])
            ->once();

        $this->app['platform.pages.handler.data']->shouldReceive('prepare')
            ->once()
            ->andReturn($data);

        $this->app['platform.pages.validator']->shouldReceive('on')
            ->with('update')
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('bind')
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('validate')
            ->once()
            ->andReturn($messages = m::mock('Illuminate\Support\MessageBag'));

        $messages->shouldReceive('isEmpty')
            ->once()
            ->andReturn(true);

        $model->shouldReceive('getAttribute')
            ->once()
            ->with('slug')
            ->andReturn('foo');

        $model->shouldReceive('getAttribute')
            ->with('uri')
            ->once()
            ->andReturn('foo');

        $model->shouldReceive('fill')
            ->once()
            ->with($data)
            ->andReturn($model);

        $model->shouldReceive('save')
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.updated', [ $model ])
            ->once();

        list($messages, $page) = $this->repository->enable(1);

        $this->assertInstanceOf('Platform\Pages\Models\Page', $page);
    }

    /** @test */
    public function it_can_disable_pages()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('where')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('first')
            ->once();

        $this->app['platform.tags']->shouldReceive('set')
            ->once();

        $this->app['platform.menus']->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $data = ['enabled' => 0];

        $model = $this->shouldReceiveFind();

        $model->shouldReceive('getAttribute')
            ->with('id')
            ->once();

        $this->app['platform.pages.validator']->shouldReceive('bypass')
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.updating', [ $model, $data ])
            ->once();

        $this->app['platform.pages.handler.data']->shouldReceive('prepare')
            ->once()
            ->andReturn($data);

        $this->app['platform.pages.validator']->shouldReceive('on')
            ->with('update')
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('bind')
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('validate')
            ->once()
            ->andReturn($messages = m::mock('Illuminate\Support\MessageBag'));

        $messages->shouldReceive('isEmpty')
            ->once()
            ->andReturn(true);

        $model->shouldReceive('getAttribute')
            ->once()
            ->with('slug')
            ->andReturn('foo');

        $model->shouldReceive('getAttribute')
            ->with('uri')
            ->once()
            ->andReturn('foo');

        $model->shouldReceive('fill')
            ->once()
            ->with($data)
            ->andReturn($model);

        $model->shouldReceive('save')
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.updated', [ $model ])
            ->once();

        list($messages, $page) = $this->repository->disable(1);

        $this->assertInstanceOf('Platform\Pages\Models\Page', $page);
    }

    /** @test */
    public function it_can_retrieve_page_files()
    {
        $this->app['config']->shouldIgnoreMissing([]);

        $paths = [__DIR__ => __DIR__];

        $this->repository = m::mock('Platform\Pages\Repositories\PageRepository[getFinder]', [$this->app]);

        $this->app['files']->shouldReceive('isDirectory')
            ->twice()
            ->andReturn(true);

        $this->repository->shouldReceive('getFinder')
            ->once()
            ->andReturn($finder = m::mock('Symfony\Component\Finder\Finder'));

        $this->app['config']->shouldReceive('get')
            ->with('platform.themes.config.active.frontend')
            ->once()
            ->andReturn('frontend::default');

        $this->app['config']->shouldReceive('get')
            ->with('platform.themes.config.fallback.frontend')
            ->once()
            ->andReturn('frontend::default');

        $this->app['themes']->shouldReceive('getCascadedViewPaths')
            ->with('frontend::default')
            ->twice()
            ->andReturn($paths);

        $this->app['view']->shouldReceive('getExtensions')
            ->once()
            ->andReturn(['blade.php' => 'blade', 'php' => '.php']);

        $finder->shouldReceive('files')
            ->once()
            ->andReturn($finder);

        $finder->shouldReceive('in')
            ->once()
            ->andReturn($finder);

        $finder->shouldReceive('getIterator')
            ->once()
            ->andReturn($iterator = m::mock('Iterator'));

        $iterator->shouldReceive('rewind')
            ->once();

        $iterator->shouldReceive('valid')
            ->once()
            ->andReturn(true);

        $iterator->shouldReceive('valid')
            ->andReturn(false);

        $iterator->shouldReceive('current')
            ->once()
            ->andReturn($file = m::mock('SplFileInfo'));

        $file->shouldReceive('getRelativePathname')
            ->andReturn(__DIR__);

        $iterator->shouldReceive('next')
            ->andReturn(__DIR__);

        $this->assertEquals($paths, $this->repository->files());
    }

    /** @test */
    public function it_can_retrieve_page_templates()
    {
        $this->app['config']->shouldIgnoreMissing([]);

        $paths = [__DIR__ => __DIR__];

        $this->repository = m::mock('Platform\Pages\Repositories\PageRepository[getFinder]', [$this->app]);

        $this->repository->shouldReceive('getFinder')
            ->once()
            ->andReturn($finder = m::mock('Symfony\Component\Finder\Finder'));

        $this->app['config']->shouldReceive('get')
            ->with('platform.themes.config.active.frontend')
            ->once()
            ->andReturn('frontend::default');

        $this->app['config']->shouldReceive('get')
            ->with('platform.themes.config.fallback.frontend')
            ->once()
            ->andReturn('frontend::default');

        $this->app['themes']->shouldReceive('getCascadedViewPaths')
            ->with('frontend::default')
            ->twice()
            ->andReturn($paths);

        $this->app['view']->shouldReceive('getExtensions')
            ->once()
            ->andReturn(['blade.php' => 'blade', 'php' => '.php']);

        $finder->shouldReceive('files')
            ->once()
            ->andReturn($finder);

        $finder->shouldReceive('depth')
            ->with('< 3')
            ->once();

        $finder->shouldReceive('exclude')
            ->once();

        $finder->shouldReceive('name')
            ->once();

        $finder->shouldReceive('in')
            ->once()
            ->andReturn($finder);

        $finder->shouldReceive('getIterator')
            ->once()
            ->andReturn($iterator = m::mock('Iterator'));

        $iterator->shouldReceive('rewind')
            ->once();

        $iterator->shouldReceive('valid')
            ->once()
            ->andReturn(true);

        $iterator->shouldReceive('valid')
            ->andReturn(false);

        $iterator->shouldReceive('current')
            ->once()
            ->andReturn($file = m::mock('SplFileInfo'));

        $file->shouldReceive('getRelativePathname')
            ->andReturn(__DIR__);

        $iterator->shouldReceive('next')
            ->andReturn(__DIR__);

        $this->assertEquals($paths, $this->repository->templates());
    }

    /** @test */
    public function it_can_create_a_new_finder_instance()
    {
        $this->assertInstanceOf('Symfony\Component\Finder\Finder', $this->repository->getFinder());
    }

    /**
     * Repository should receive createModel.
     *
     * @return mixed
     */
    protected function shouldReceiveCreateModel($times = 1)
    {
        $model = m::mock('Platform\Pages\Models\Page');

        $this->repository->shouldReceive('createModel')
            ->times($times)
            ->andReturn($model);

        return $model;
    }

    /*
     * Find method expectation.
     *
     * @param  mixed  $model
     * @param  bool  $returnModel
     * @return mixed
     */
    protected function shouldReceiveFind($model = null, $returnModel = true)
    {
        $model = $model ?: m::mock('Platform\Pages\Models\Page');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $cacheExpectation = $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.page.1', m::on(function ($callback) {
                $callback();
                return true;
            }));

        $modelExpectation = $model->shouldReceive('find')
            ->once();

        if ($returnModel) {
            $modelExpectation->andReturn($model);
            $cacheExpectation->andReturn($model);
        }

        return $model;
    }

    /**
     * Page creation expectations.
     *
     * @param  arary  $data
     * @return \Platform\Pages\Models\Page
     */
    protected function shouldReceiveCreate($data)
    {
        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.creating', [ $data ])
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.created', m::any())
            ->once();

        $this->app['platform.pages.handler.data']->shouldReceive('prepare')
            ->once()
            ->andReturn($data);

        $this->app['platform.pages.validator']->shouldReceive('on')
            ->with('create')
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('validate')
            ->once()
            ->with($data)
            ->andReturn($messages = m::mock('Illuminate\Support\MessageBag'));

        $messages->shouldReceive('isEmpty')
            ->once()
            ->andReturn(true);

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model = m::mock('Platform\Pages\Models\Page'));

        $model->shouldReceive('fill')
            ->once()
            ->with($data)
            ->andReturn($model);

        $model->shouldReceive('save')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('id')
            ->once()
            ->andReturn(1);

        return $model;
    }

    /**
     * Page update expectations.
     *
     * @param  arary  $data
     * @return void
     */
    protected function shouldReceiveUpdate($data)
    {
        $model = $this->shouldReceiveFind();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.updating', [ $model, $data ])
            ->once();

        // Strip tags from data to prepare
        $data = array_except($data, 'tags');

        $this->app['platform.pages.handler.data']->shouldReceive('prepare')
            ->once()
            ->andReturn($data);

        $model->shouldReceive('getAttribute')
            ->once()
            ->with('slug')
            ->andReturn('foo');

        $model->shouldReceive('getAttribute')
            ->once()
            ->with('uri')
            ->andReturn('foo');

        $this->app['events']->shouldReceive('fire')
            ->with('platform.page.updated', [ $model ])
            ->once();

        $this->app['platform.pages.validator']->shouldReceive('on')
            ->with('update')
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('bind')
            ->with($data)
            ->once()
            ->andReturn($this->app['platform.pages.validator']);

        $this->app['platform.pages.validator']->shouldReceive('validate')
            ->once()
            ->andReturn($messages = m::mock('Illuminate\Support\MessageBag'));

        $messages->shouldReceive('isEmpty')
            ->once()
            ->andReturn(true);

        $model->shouldReceive('fill')
            ->once()
            ->with($data)
            ->andReturn($model);

        $model->shouldReceive('save')
            ->once();

        $model->shouldReceive('getAttribute')
            ->with('id')
            ->once();
    }
}
