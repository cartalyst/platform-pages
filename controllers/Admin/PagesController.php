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
use Config;
use DataGrid;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Redirect;
use Symfony\Component\Finder\Finder;
use Theme;
use View;

class PagesController extends AdminController {

	/**
	 * Holds the pages model.
	 *
	 * @var \Platform\Pages\Models\Page
	 */
	protected $pageModel = null;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->pageModel = app('Platform\Pages\Models\Page');
	}

	/**
	 * Display a listing of pages.
	 *
	 * @return \Illuminate\View\View
	 */
	public function getIndex()
	{
		// Show the page
		return View::make('platform/pages::index');
	}

	/**
	 * Datasource for the pages Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function getGrid()
	{
		// Get all the pages
		$response = API::get('v1/pages');

		// Return the Data Grid object
		return DataGrid::make($response['pages'], array(
			'id',
			'name',
			'slug',
			'uri',
			'enabled',
			'created_at',
		));
	}

	/**
	 * Show the form for creating a new page.
	 *
	 * @return mixed
	 */
	public function getCreate()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating a new page.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postCreate()
	{
		return $this->processForm();
	}

	/**
	 * Show the form for updating a page.
	 *
	 * @param  mixed  $slug
	 * @return mixed
	 */
	public function getEdit($slug = null)
	{
		// Do we have a page identifier?
		if ( ! $slug)
		{
			return Redirect::toAdmin('pages');
		}

		return $this->showForm('edit', $slug);
	}

	/**
	 * Handle posting of the form for updating a page.
	 *
	 * @param  mixed  $slug
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postEdit($slug = null)
	{
		return $this->processForm($slug);
	}

	/**
	 * Show the form for copying a page.
	 *
	 * @param  mixed  $slug
	 * @return mixed
	 */
	public function getCopy($slug = null)
	{
		// Do we have a page identifier?
		if ( ! $slug)
		{
			return Redirect::toAdmin('pages');
		}

		return $this->showForm('copy', $slug);
	}

	/**
	 * Handle posting of the form for copying a page.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postCopy()
	{
		return $this->processForm();
	}

	/**
	 * Remove the specified page.
	 *
	 * @param  mixed  $slug
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function getDelete($slug = null)
	{
		try
		{
			API::delete("v1/page/{$slug}");

			// Set the success message
			return Redirect::toAdmin('pages')->withSuccess(Lang::get('platform/pages::message.success.delete'));
		}
		catch (ApiHttpException $e)
		{
			return Redirect::toAdmin('pages')->withErrors(Lang::get('platform/pages::message.success.delete'));
		}
	}

	/**
	 * Shows the form.
	 *
	 * @param  string  $mode
	 * @param  mixed   $slug
	 * @return mixed
	 */
	protected function showForm($mode = null, $slug = null)
	{
		try
		{
			// Do we have a page identifier?
			if ($slug)
			{
				// Get the page information
				$response = API::get("v1/page/{$slug}");
				$page = $response['page'];

				// See if we have a menu assigned to this page
				$response = API::get("v1/menus?criteria[page_id]={$page->id}&return=first");
				$menu = $response['menu'];
			}
			else
			{
				$page = app('Platform\Pages\Models\Page');
			}

			// Get all the available user groups
			$response = API::get('v1/users/groups');
			$groups = $response['groups'];

			// Get all the available templates
			$templates = $this->pageModel->getTemplates();

			// Get the default template
			$defaultTemplate = Config::get('platform/pages::template', null);

			// Get all the available page files
			$files = $this->pageModel->getPageFiles();

			// Get the root items
			$response = API::get('v1/menus?root=true');
			$menus = $response['menus'];

			// Show the page
			return View::make('platform/pages::form', compact('page', 'groups', 'templates', 'defaultTemplate', 'files', 'menus', 'menu', 'mode'));
		}
		catch (ApiHttpException $e)
		{
			return Redirect::toAdmin('pages')->withErrors($e->getMessage());
		}
	}

	/**
	 * Processes the form.
	 *
	 * @param  mixed  $slug
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function processForm($slug = null)
	{
		try
		{
			// Do we have a page identifier?
			if ($slug)
			{
				// Make the request
				$response = API::put("v1/pages/{$slug}", Input::all());

				// Prepare the success message
				$message = Lang::get('platform/pages::message.success.edit');
			}
			else
			{
				// Make the request
				$response = API::post('v1/pages', Input::all());

				// Prepare the success message
				$message = Lang::get('platform/pages::message.success.create');
			}

			// Get the page slug
			$slug = $response['page']->slug;

			// Redirect to the page edit page
			return Redirect::toAdmin("pages/edit/{$slug}")->withSuccess($message);
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the appropriate page
			return Redirect::back()->withInput()->withErrors($e->getErrors());
		}
	}

}
