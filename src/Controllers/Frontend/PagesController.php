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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Sentry;
use Config;
use Platform\Foundation\Controllers\BaseController;
use Platform\Pages\Repositories\PageRepositoryInterface;
use Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PagesController extends BaseController {

	/**
	 * The Pages repository.
	 *
	 * @var \Platform\Pages\Repositories\PageRepositoryInterface
	 */
	protected $pages;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(PageRepositoryInterface $pages)
	{
		parent::__construct();

		$this->pages = $pages;
	}

	/**
	 * Render the page.
	 *
	 * @return mixed
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 */
	public function page()
	{
		// Make sure we have a page slug
		$slug = Route::current()->getUri() ?: Config::get('platform/pages::default');

		// Find the requested page
		if ( ! $page = $this->pages->findEnabled($slug))
		{
			throw new HttpException(404, 'Page does not exist.');
		}

		if (in_array($page->visibility, ['logged_in', 'admin']))
		{
			$canView = false;

			if ($currentUser = Sentry::getUser())
			{
				$canView = true;

				if ( ! Sentry::hasAccess('admin'))
				{
					if ($page->visibility === 'admin')
					{
						$canView = false;
					}
					else if ( ! empty($page->groups))
					{
						$canView = false;

						foreach ($currentUser->groups as $group)
						{
							if (in_array($group->id, $page->groups))
							{
								$canView = true;

								break;
							}
						}
					}
				}
			}

			if ( ! $canView)
			{
				throw new HttpException(403, "You don't have permission to view this page.");
			}
		}

		return $page->render();
	}

}

