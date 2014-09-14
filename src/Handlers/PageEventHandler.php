<?php namespace Platform\Pages\Handlers;
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

use Illuminate\Events\Dispatcher;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Platform\Pages\Models\Page;
use Platform\Pages\Repositories\PageRepositoryInterface;

class PageEventHandler implements PageEventHandlerInterface {

	/**
	 * The container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * The pages repository.
	 *
	 * @var \Platform\Pages\Repositories\PageRepositoryInterface
	 */
	protected $pages;

 	/**
	 * The Cache manager instance.
	 *
	 * @var \Illuminate\Cache\CacheManager
	 */
	protected $cache;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Pages\Repositories\PageRepositoryInterface  $pages
	 * @param  \Illuminate\Container\Container  $app
	 * @param  \Illuminate\Cache\CacheManager  $cache
	 * @return void
	 */
	public function __construct(PageRepositoryInterface $pages, Container $app, CacheManager $cache)
	{
		$this->app = $app;

		$this->cache = $cache;

		$this->pages = $pages;

	}

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('platform.page.created', 'Platform\Pages\Handlers\PageEventHandler@onCreate');

		$dispatcher->listen('platform.page.updated', 'Platform\Pages\Handlers\PageEventHandler@onUpdate');

		$dispatcher->listen('platform.page.deleted', 'Platform\Pages\Handlers\PageEventHandler@onDelete');
	}

	/**
	 * {@inheritDoc}
	 */
	public function onCreate(Page $page)
	{
		$this->cache->forget('platform.page.all');
		$this->cache->forget('platform.page.all.enabled');

		$this->pages->find($page->id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onUpdate(Page $page)
	{
		$this->cache->forget('platform.page.all');
		$this->cache->forget('platform.page.all.enabled');

		$this->cache->forget("platform.page.{$page->id}");
		$this->cache->forget("platform.page.{$page->slug}");
		$this->cache->forget("platform.page.{$page->uri}");
		$this->cache->forget("platform.page.enabled.{$page->id}");
		$this->cache->forget("platform.page.enabled.{$page->slug}");
		$this->cache->forget("platform.page.enabled.{$page->uri}");

		$this->pages->find($page->id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onDelete(Page $page)
	{
		$this->cache->forget('platform.page.all');
		$this->cache->forget('platform.page.all.enabled');

		$this->cache->forget("platform.page.{$page->id}");
		$this->cache->forget("platform.page.{$page->slug}");
		$this->cache->forget("platform.page.{$page->uri}");
		$this->cache->forget("platform.page.enabled.{$page->id}");
		$this->cache->forget("platform.page.enabled.{$page->slug}");
		$this->cache->forget("platform.page.enabled.{$page->uri}");
	}

}
