<?php namespace Platform\Pages\Controllers\Frontend;
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Config;
use Platform\Pages\Repositories\PageRepositoryInterface;
use Platform\Foundation\Controllers\BaseController;
use Route;
use Sentry;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PagesController extends BaseController {

	/**
	 * Pages repository.
	 *
	 * @var \Platform\Pages\Repositories\PagesRepositoryInterface
	 */
	protected $pages;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(PageRepositoryInterface $pages)
	{
		$this->pages = $pages;
	}

	/**
	 * Render the page.
	 *
	 * @return mixed
	 * @throws \HttpException
	 */
	public function page()
	{
		// Make sure we have a page slug
		$slug = Route::current()->getUri() ?: Config::get('platform/pages::default');

		// Find the requested page
		$page = $this->pages->findEnabled($slug);

		if ( ! $page)
		{
			throw new HttpException(403, "Page does not exist.");
		}

		// @todo: We should have a config item whether invalid
		// perms for pages should throw a 404, 403 or redirect
		// to a certain page...
		if (in_array($page->visibility, array('logged_in', 'admin')))
		{
			if ( ! $currentUser = Sentry::getUser())
			{
				throw new HttpException(403, "You don't have permission to view this page.");
			}

			if ( ! $currentUser->isSuperUser() and ! empty($page->groups))
			{
				$found = false;

				foreach ($currentUser->groups as $group)
				{
					if (in_array($group->id, $page->groups))
					{
						$found = true;

						break;
					}
				}

				if ( ! $found)
				{
					throw new HttpException(403, "You don't have permission to view this page.");
				}
			}
		}

		return $page->render();
	}

}

