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
use Platform\Pages\Repositories\PageRepositoryInterface;
use Redirect;
use View;

class PagesController extends AdminController {

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
		parent::__construct();

		$this->pages = $pages;
	}

	/**
	 * Display a listing of pages.
	 *
	 * @return \Illuminate\View\View
	 */
	public function getIndex()
	{
		return View::make('platform/pages::index');
	}

	/**
	 * Datasource for the pages Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function getGrid()
	{
		return DataGrid::make($this->pages->grid(), array(
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
	 * @return \Illuminate\View\View
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
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating a page.
	 *
	 * @param  mixed  $slug
	 * @return mixed
	 */
	public function getEdit($slug = null)
	{
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
		return $this->processForm('update', $slug);
	}

	/**
	 * Show the form for copying a page.
	 *
	 * @param  mixed  $slug
	 * @return mixed
	 */
	public function getCopy($slug = null)
	{
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
		return $this->processForm('create');
	}

	/**
	 * Remove the specified page.
	 *
	 * @param  mixed  $slug
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function getDelete($slug = null)
	{
		// Do we have a page identifier?
		if ($slug)
		{
			// Delete the page
			$this->pages->delete($slug);

			// Prepare the success message
			$message = Lang::get('platform/pages::message.success.delete');

			// Redirect to the pages management page
			return Redirect::toAdmin('pages')->withSuccess($message);
		}

		return Redirect::toAdmin('pages');
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
		// Do we have a page identifier?
		if ($slug)
		{
			$page = $this->pages->find($slug);

			// See if we have a menu assigned to this page
			$response = API::get("v1/menus?criteria[page_id]={$page->id}&return=first");
			$menu = $response['menu'];
			//$menu = $this->menus->findWhere('page_id', $page->id);
			# need to figure out the best way to handle these searches..
		}
		else
		{
			$page = $this->pages->createModel();
		}

		// Get all the available user groups
		$response = API::get('v1/users/groups');
		$groups = $response['groups'];
		//$groups = $this->groups->findAll();

		// Get all the available templates
		$templates = $this->pages->templates();

		// Get the default template
		$defaultTemplate = Config::get('platform/pages::template', null);

		// Get all the available page files
		$files = $this->pages->files();

		// Get the root items
		$response = API::get('v1/menus?root=true');
		$menus = $response['menus'];
		//$menus = $this->menus->findRoot();

		// Show the page
		return View::make('platform/pages::form', compact('mode', 'page', 'groups', 'templates', 'defaultTemplate', 'files', 'menus', 'menu'));
	}

	/**
	 * Processes the form.
	 *
	 * @param  string  $mode
	 * @param  mixed   $slug
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function processForm($mode, $slug = null)
	{
		// Get the input data
		$input = Input::all();

		// Do we have a page identifier?
		if ($slug)
		{
			// Check if the input is valid
			$messages = $this->pages->validForUpdate($slug, $input);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Update the page
				$page = $this->pages->update($slug, $input);
			}
		}
		else
		{
			// Check if the input is valid
			$messages = $this->pages->validForCreation($input);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Create the pages
				$page = $this->pages->create($input);
			}
		}

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			// Prepare the success message
			$message = Lang::get("platform/pages::message.success.{$mode}");

			return Redirect::toAdmin("pages/edit/{$page->slug}")->withSuccess($message);
		}

		return Redirect::back()->withInput()->witherrors($messages);
	}

}
