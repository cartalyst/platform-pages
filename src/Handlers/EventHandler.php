<?php namespace Platform\Pages\Handlers;
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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Pages\Models\Page;
use Illuminate\Events\Dispatcher;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class EventHandler extends BaseEventHandler implements EventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('platform.page.creating', __CLASS__.'@creating');
		$dispatcher->listen('platform.page.created', __CLASS__.'@created');

		$dispatcher->listen('platform.page.updating', __CLASS__.'@updating');
		$dispatcher->listen('platform.page.updated', __CLASS__.'@updated');

		$dispatcher->listen('platform.page.deleted', __CLASS__.'@deleted');
	}

	/**
	 * {@inheritDoc}
	 */
	public function creating(array $input)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function created(Page $page)
	{
		$this->flushCache($page);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Page $page, array $input)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Page $page)
	{
		$this->flushCache($page);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Page $page)
	{
		$this->flushCache($page);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Platform\Pages\Models\Page  $page
	 * @return void
	 */
	protected function flushCache(Page $page)
	{
		$cacheKeys = [ $page->id, $page->slug, $page->uri ];

		$this->app['cache']->forget('platform.pages.all');
		$this->app['cache']->forget('platform.pages.all.enabled');

		$this->app['cache']->forget('platform.page.'.$cacheKeys[0]);
		$this->app['cache']->forget('platform.page.slug.'.$cacheKeys[1]);
		$this->app['cache']->forget('platform.page.uri.'.$cacheKeys[2]);

		foreach ($cacheKeys as $key)
		{
			$this->app['cache']->forget('platform.page.enabled.'.$key);
		}
	}

}
