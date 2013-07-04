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

use API;
use Cartalyst\Api\Http\ApiHttpException;
use Config;
use Platform\Routing\Controllers\BaseController;
use Sentry;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PagesController extends BaseController {

	/**
	 *
	 *
	 * @param  string  $slug
	 * @return mixed
	 * @throws NotFoundHttpException
	 */
	public function getPage($slug = null)
	{
		// Default the page slug
		$slug = $slug ?: Config::get('platform/pages::default');

		try
		{
			// Find the requested page
			$response = API::get("v1/pages/$slug", array('enabled' => true));
			$page     = $response['page'];
		}
		catch (ApiHttpException $e)
		{
			throw new NotFoundHttpException("No matching page could be found for the requested URI [$slug].");
		}

		// @todo: We should have a config item whether invalid
		// perms for pages should throw a 404, 403 or redirect
		// to a certain page...
		if (in_array($page->visibility, array('logged_in', 'admin')))
		{
			if ( ! Sentry::check())
			{
				throw new HttpException(403, "You don't have permission to view this page.");
			}

			if ( ! Sentry::getUser()->isSuperUser())
			{
				if ($page->groups->count())
				{
					$found = false;

					foreach (Sentry::getUser()->groups() as $group)
					{
						if ($page->groups->find($group->getKey()))
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
		}

		return $page->render();
	}

}

