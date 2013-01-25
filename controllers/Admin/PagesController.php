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
	 * Pages management main page.
	 *
	 * @return mixed
	 */
	public function getIndex()
	{
		// Set the current active menu
		set_active_menu('admin-pages');

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

	/**
	 * Create a new page.
	 *
	 * @return mixed
	 */
	public function getCreate()
	{
		// Set the current active menu
		set_active_menu('admin-pages');

		try
		{
			// Get the available storage types
			$storageTypes = pagesStorageTypes();

			// Get all the available frontend templates
			$templates = pagesFindTemplates();

			// Get the pages visibility statuses
			$visibility = pagesVisibilityStatuses();

			// Get all the available user groups
			$request = \API::get('users/groups', array('organized' => true));
			$groups  = $request['groups'];
		}
		catch (\Cartalyst\Api\ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Redirect to the pages management page
			return \Redirect::to(ADMIN_URI . '/pages');
		}

		// Show the page
		return \View::make('platform/pages::create', compact('storageTypes', 'templates', 'visibility', 'groups'));
	}

	/**
	 * Create a new page form processing.
	 *
	 * @return Redirect
	 */
	public function postCreate()
	{
		return $this->postEdit();
	}

	/**
	 * Page update.
	 *
	 * @param  int  $pageId
	 * @return mixed
	 */
	public function getEdit($pageId = null)
	{
		// Set the current active menu
		set_active_menu('admin-pages');

		try
		{
			// Get the page information
			$request = \API::get('pages/' . $pageId);
			$page    = $request['page'];

			// Get the available storage types
			$storageTypes = pagesStorageTypes();

			// Get all the available frontend templates
			$templates = pagesFindTemplates();

			// Get the pages visibility statuses
			$visibility = pagesVisibilityStatuses();

			// Get all the available user groups
			$request = \API::get('users/groups', array('organized' => true));
			$groups  = $request['groups'];
		}
		catch (\Cartalyst\Api\ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Return to the page management page
			return \Redirect::to(ADMIN_URI . '/pages');
		}

		// Show the page
		return \View::make('platform/pages::edit', compact('page', 'storageTypes', 'templates', 'visibility', 'groups'));
	}

	/**
	 * Page update form processing.
	 *
	 * @param  int  $pageId
	 * @return Redirect
	 */
	public function postEdit($pageId = null)
	{
		// Url so we know to where redirect the page to
		$url = (is_null($pageId) ? 'create' : 'edit/' . $pageId);

		try
		{
			// Are we creating a page?
			if (is_null($pageId))
			{
				$request = \API::post('pages', \Input::all());
			}

			// No, we are updating an existing page
			else
			{
				$request = \API::put('pages/' . $pageId, \Input::all());
			}


			# Temporary solution for Validation
			if (isset($request['validator']))
			{
				$validator = $request['validator'];

				// Check if the validation failed
				if ($validator->fails())
				{
					// Redirect to the appropriate page
					return \Redirect::to(ADMIN_URI . '/pages/' . $url)->withInput()->withErrors($validator);
				}
			}
			#


			// Set the success message
			# TODO !
		}
		catch (\Cartalyst\Api\ApiHttpException $e)
		{
			// Set the error message
			# TODO !
		}

		// Redirect to the appropriate page
		return \Redirect::to(ADMIN_URI . '/pages/' . $url)->withInput();
	}

	/**
	 * Page delete.
	 *
	 * @param  int  $pageId
	 * @return Redirect
	 */
	public function getDelete($pageId = null)
	{
		try
		{
			// Delete the page
			\API::delete('pages/' . $pageId);

			// Set the success message
			# TODO !
		}
		catch (\Cartalyst\Api\ApiHttpException $e)
		{
			// Set the error message.
			# TODO !
		}

		// Redirect to the pages management
		return \Redirect::to(ADMIN_URI . '/pages');
	}

}
