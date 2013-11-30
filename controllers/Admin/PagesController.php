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

use Config;
use DataGrid;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Platform\Menus\Repositories\MenuRepositoryInterface;
use Platform\Pages\Repositories\PageRepositoryInterface;
use Platform\Users\Repositories\GroupRepositoryInterface;
use Redirect;
use View;

class PagesController extends AdminController {

	/**
	 * Pages repository.
	 *
	 * @var \Platform\Pages\Repositories\PageRepositoryInterface
	 */
	protected $pages;

	/**
	 * Menus repository.
	 *
	 * @var \Platform\Menus\Repositories\MenuRepositoryInterface
	 */
	protected $menus;

	/**
	 * Groups repository.
	 *
	 * @var \Platform\Users\Repositories\GroupRepositoryInterface
	 */
	protected $groups;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(
		PageRepositoryInterface $pages,
		MenuRepositoryInterface $menus,
		GroupRepositoryInterface $groups
	)
	{
		parent::__construct();

		$this->pages = $pages;

		$this->menus = $menus;

		$this->groups = $groups;
	}

	/**
	 * Display a listing of pages.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return View::make('platform/pages::index');
	}

	/**
	 * Datasource for the pages Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
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
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating a new page.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating a page.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating a page.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Show the form for copying a page.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function copy($id)
	{
		return $this->showForm('copy', $id);
	}

	/**
	 * Remove the specified page.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		// Delete the page
		if ($this->pages->delete($id))
		{
			return Redirect::toAdmin('pages')->withSuccess(Lang::get('platform/pages::message.success.delete'));
		}

		return Redirect::toAdmin('pages')->withErrors(Lang::get('platform/pages::message.error.delete'));
	}

	/**
	 * Shows the form.
	 *
	 * @param  string  $mode
	 * @param  int     $id
	 * @return mixed
	 */
	protected function showForm($mode, $id = null)
	{
		// Do we have a page identifier?
		if ($id)
		{
			$page = $this->pages->find($id);
		}
		else
		{
			$page = $this->pages->createModel();
		}

		if ( ! $page)
		{
			return Redirect::toAdmin('pages')->withErrors(Lang::get('platform/pages::message.not_found', array('id' => $id)));
		}

		$menu = $this->menus->findWhere('page_id', $page->id);

		// Get all the available user groups
		$groups = $this->groups->findAll();

		// Get all the available templates
		$templates = $this->pages->templates();

		// Get the default template
		$defaultTemplate = Config::get('platform/pages::template', null);

		// Get all the available page files
		$files = $this->pages->files();

		// Get the root items
		$menus = $this->menus->findRoot();

		// Show the page
		return View::make('platform/pages::form', compact('mode', 'page', 'groups', 'templates', 'defaultTemplate', 'files', 'menus', 'menu'));
	}

	/**
	 * Processes the form.
	 *
	 * @param  string  $mode
	 * @param  int     $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function processForm($mode, $id = null)
	{
		// Get the input data
		$input = Input::all();

		// Do we have a page identifier?
		if ($id)
		{
			// Check if the input is valid
			$messages = $this->pages->validForUpdate($id, $input);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Update the page
				$page = $this->pages->update($id, $input);
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

			return Redirect::toAdmin("pages/{$page->id}/edit")->withSuccess($message);
		}

		return Redirect::back()->withInput()->witherrors($messages);
	}

}
