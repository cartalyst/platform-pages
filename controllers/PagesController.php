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

use Platform\Routing\Controllers\AdminController;

class PagesController extends AdminController {

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
			$request = \API::get('pages/' . $pageSlug, array('status' => 1));
			$page    = $request['page'];

			// Check if the current user can see this page
			if ( ! \Sentry::check() and $page->visibility)
			{
				# IMO we should show a View page, instead of redirecting
				# the user to that page...
				#
				# return \Theme::make('errors/invalid-permissions');
				# the errors are saved within the themes folder
				# so the designer/developer can have more controll
				# of how the page should look like

				return \Redirect::to('invalid_permissions');
			}

			// Does this page have user groups assigned?
			if ($groups = $page->groups())
			{
				// Pretend the user doesn't have access
				$inGroups = false;

				// Loop through the groups
				foreach ($groups as $groupId)
				{
					try
					{
						// Get this group information
						$group = \Sentry::getGroupProvider()->findById($groupId);

						// Check if the user is in this group
						if (\Sentry::getUser()->inGroup($group))
						{
							// The user has access to this group, sweet!
							$inGroups = true;

							break;
						}
					}
					catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
					{
						# we maybe throw a 404 error here, because,
						# the group wasn't found, the possible reason
						# is that the group was deleted, need to test
						# this more
						#
						echo 'Group does not exist :c';
						die;
					}
				}

				// If the user isn't assigned to any of the page
				// groups, it means that this user is not allowed
				// to view this page.
				if ( ! $inGroups)
				{
					# same as the above, show the insufficient permisions page...
					echo 'you don\'t have access';
					die;
				}
			}



			### TODO AFTER THIS ###

			echo 'You are on the requested page!';
			return;


			// Is this page content saved on a file?
			if ($page->type === 'file')
			{
				// Show the page
				return View::make('platform/pages::files.' . $page->slug);
			}

			// Render the page content value
			$content = content_render($page->value);

			// Show the page
			return View::make('templates.layouts.' . $page->template, compact('content'));
		}
		catch (\Cartalyst\Api\ApiHttpException $e)
		{
			// The page doesn't exist
			# App::abort() or something here to show the 404 page !
			echo 'The requested page was not found!';
			die;
		}
	}

}
