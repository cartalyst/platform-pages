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
use Platform\Pages\Models\Page;

interface PageEventHandlerInterface {

	/**
	 * Registers the event listeners using the given dispatcher instance.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function subscribe(Dispatcher $dispatcher);

	/**
	 * When page is created.
	 *
	 * @param  \Platform\Pages\Models\Page  $page
	 * @return void
	 */
	public function onCreate(Page $page);

	/**
	 * When page is updated.
	 *
	 * @param  \Platform\Pages\Models\Page  $page
	 * @return void
	 */
	public function onUpdate(Page $page);

	/**
	 * When page is deleted.
	 *
	 * @param  \Platform\Pages\Models\Page  $page
	 * @return void
	 */
	public function onDelete(Page $page);

}
