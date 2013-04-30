<?php namespace Platform\Pages\Controllers;
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
use Cartalyst\Api\ApiHttpException;
use Platform\Routing\Controllers\FrontendController;
use Sentry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PagesController extends FrontendController {

	/**
	 *
	 *
	 * @param  string  $pageSlug
	 * @return mixed
	 */
	public function getPage($pageSlug = null)
	{
		// Make sure we have a proper page slug
		$pageSlug = ($pageSlug ?: 'welcome'); # getSetting('platform/pages::default.page');

		try
		{
			// Find the requested page
			$request = API::get('pages/' . $pageSlug, array('enabled' => 1));
			$page    = $request['page'];
		}
		catch (ApiHttpException $e)
		{
			throw new NotFoundHttpException("No matching page could be found for the requested URI.");
		}

		// @todo: We should have a config item whether invalid
		// perms for pages should throw a 404, 403 or redirect
		// to a certain page...
		if ($page->visibility == 'logged_in')
		{
			if ( ! Sentry::check())
			{
				throw new NotFoundHttpException;
			}

			foreach (Sentry::getUser()->groups() as $group)
			{
				$found = false;

				if ($page->groups->find($group->getKey()))
				{
					$found = true;
					break;
				}

				if ($found == false)
				{
					throw new NotFoundHttpException;
				}
			}
		}

		// Is this page content saved on a file?
		if ($page->type === 'filesystem')
		{
			// Show the page
			return View::make($page->file);
		}

		// Render the page content value
		$content = content_render($page->value);

		// Show the page
		return View::make($page->template, compact('content'));
	}

}
