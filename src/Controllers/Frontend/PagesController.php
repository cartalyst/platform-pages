<?php namespace Platform\Pages\Controllers\Frontend;
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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Config;
use Platform\Foundation\Controllers\BaseController;
use Platform\Pages\Repositories\PageRepositoryInterface;
use Route;
use Sentinel;
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

			if ($currentUser = Sentinel::getUser())
			{
				$canView = true;

				if ( ! Sentinel::hasAccess('admin'))
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

		return $this->pages->render($page);
	}

}

