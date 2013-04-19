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

use API;
use Cartalyst\Api\Http\ApiHttpException;
use DataGrid;
use Illuminate\Support\MessageBag as Bag;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Platform\Pages\Page;
use Redirect;
use View;

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
			$response = API::get('pages');
			$pages    = $response['pages'];
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the admin dashboard
			return Redirect::toAdmin('');
		}

		// Show the page
		return View::make('platform/pages::index', compact('pages'));
	}

	/**
	 * Datasource for the pages Data Grid.
	 *
	 * @return Cartalyst\DataGrid\DataGrid
	 */
	public function getGrid()
	{
		return DataGrid::make(with(new Page)->newQuery(), array(
			'id',
			'name',
			'slug',
			'status',
			'created_at',
		));
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
			$storageTypes = pages_storage_types();

			// Get all the available frontend templates
			$templates = pages_find_templates();

			// Get the pages visibility statuses
			$visibility = pages_visibility_statuses();

			// Get all the available user groups
			$response = API::get('users/groups', array('organized' => true));
			$groups   = $response['groups'];

			// Selected groups
			$selectedGroups = Input::old('groups', array());
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the pages management page
			return Redirect::toAdmin('pages');
		}

		// Show the page
		return View::make('platform/pages::create', compact('storageTypes', 'templates', 'visibility', 'groups', 'selectedGroups'));
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
			$response = API::get("pages/$pageId");
			$page     = $response['page'];

			// Get this page groups
			$pageGroups = $page->groups();

			// Get the available storage types
			$storageTypes = pages_storage_types();

			// Get all the available frontend templates
			$templates = pages_find_templates();

			// Get the pages visibility statuses
			$visibility = pages_visibility_statuses();

			// Get all the available user groups
			$response = API::get('users/groups', array('organized' => true));
			$groups   = $response['groups'];
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$messages = with(new Bag)->add('error', $e->getMessage());

			// Return to the pages management page
			return Redirect::toAdmin('pages')->with('messages', $messages);
		}

		// Show the page
		return View::make('platform/pages::edit', compact('page', 'storageTypes', 'templates', 'visibility', 'groups', 'pageGroups'));
	}

	/**
	 * Page update form processing.
	 *
	 * @param  int  $pageId
	 * @return Redirect
	 */
	public function postEdit($pageId = null)
	{
		try
		{
			// Are we creating a new page?
			if (is_null($pageId))
			{
				$response = API::post('pages', Input::all());
				$pageId = $response['page']->id;

				// Prepare the success message
				$success = Lang::get('platform/pages::message.create.success');
			}

			// No, we are updating an existing page
			else
			{
				API::put("pages/$pageId", Input::all());

				// Prepare the success message
				$success = Lang::get('platform/pages::message.update.success');
			}

			// Set the success message
			$messages = with(new Bag)->add('success', $success);

			// Redirect to the page edit page
			return Redirect::toAdmin("pages/edit/$pageId")->with('messages', $messages);
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the appropriate page
			return Redirect::back()->withInput()->withErrors($e->getErrors());
		}
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
			API::delete("pages/$pageId");

			// Set the success message
			$messages = with(new Bag)->add('success', Lang::get('platform/pages::message.delete.success'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$messages = with(new Bag)->add('error', Lang::get('platform/pages::message.delete.error'));
		}

		// Redirect to the pages management page
		return Redirect::toAdmin('pages')->with('messages', $messages);
	}

	/**
	 * Page clone.
	 *
	 * @param  int  $pageId
	 * @return mixed
	 */
	public function getClone($pageId = null)
	{
		try
		{
			// Get the page information
			$response = API::get("pages/$pageId");
			$page     = $response['page'];

			// Get this page groups
			$pageGroups = $page->groups();

			// Get the available storage types
			$storageTypes = pages_storage_types();

			// Get all the available frontend templates
			$templates = pages_find_templates();

			// Get the pages visibility statuses
			$visibility = pages_visibility_statuses();

			// Get all the available user groups
			$response = API::get('users/groups', array('organized' => true));
			$groups   = $response['groups'];
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Return to the page management page
			return Redirect::toAdmin('pages');
		}

		// Show the page
		return View::make('platform/pages::clone', compact('page', 'storageTypes', 'templates', 'visibility', 'groups', 'pageGroups'));
	}

	/**
	 * Page clone form processing.
	 *
	 * @param  int  $pageId
	 * @return Redirect
	 */
	public function postClone($pageId = null)
	{
		return $this->postEdit();
	}

}
