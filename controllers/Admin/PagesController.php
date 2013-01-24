<?php namespace Platform\Pages\Controllers\Admin;
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
	 * Page management main page.
	 *
	 * @return View
	 */
	public function getIndex()
	{
		// Set the current active menu
		#set_active_menu('admin-cms-pages');

		try
		{
			// Get the pages list
			$request = \API::get('pages', array(
				'limit' => 10
			));
			$pages = $request['pages'];
		}
		catch (\Cartalyst\Api\ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Redirect to the admin dashboard
			return \Redirect::to(ADMIN_URI);
		}

		// Show the page
		return \View::make('platform/pages::index', compact('pages'));
	}


}
