<?php namespace Platform\Pages\Controllers\Admin;
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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Lang;
use View;
use Input;
use Config;
use DataGrid;
use Redirect;
use Response;
use Platform\Admin\Controllers\Admin\AdminController;
use Platform\Menus\Repositories\MenuRepositoryInterface;
use Platform\Pages\Repositories\PageRepositoryInterface;
use Platform\Users\Repositories\RoleRepositoryInterface;

class PagesController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Pages repository.
	 *
	 * @var \Platform\Pages\Repositories\PageRepositoryInterface
	 */
	protected $pages;

	/**
	 * The Menus repository.
	 *
	 * @var \Platform\Menus\Repositories\MenuRepositoryInterface
	 */
	protected $menus;

	/**
	 * The Roles repository.
	 *
	 * @var \Platform\Users\Repositories\RoleRepositoryInterface
	 */
	protected $roles;

	/**
	 * Holds all the mass actions we can execute.
	 *
	 * @var array
	 */
	protected $actions = [
		'delete',
		'enable',
		'disable',
	];

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Users\Repositories\PageRepositoryInterface  $pages
	 * @param  \Platform\Users\Repositories\MenuRepositoryInterface  $menus
	 * @param  \Platform\Users\Repositories\RoleRepositoryInterface  $roles
	 * @return void
	 */
	public function __construct(
		PageRepositoryInterface $pages,
		MenuRepositoryInterface $menus,
		RoleRepositoryInterface $roles
	)
	{
		parent::__construct();

		$this->pages = $pages;

		$this->menus = $menus;

		$this->roles = $roles;
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
		$data = $this->pages->grid();

		$columns = [
			'id',
			'name',
			'slug',
			'uri',
			'enabled',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		return DataGrid::make($data, $columns, $settings);
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
		if ($this->pages->delete($id))
		{
			$message = Lang::get('platform/pages::message.success.delete');

			return Redirect::toAdmin('pages')->withSuccess($message);
		}

		$message = Lang::get('platform/pages::message.error.delete');

		return Redirect::toAdmin('pages')->withErrors($message);
	}

	/**
	 * Executes the mass action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function executeAction()
	{
		$action = Input::get('action');

		if (in_array($action, $this->actions))
		{
			foreach (Input::get('entries', []) as $entry)
			{
				$this->pages->{$action}($entry);
			}

			return Response::make('Success');
		}

		return Response::make('Failed', 500);
	}

	/**
	 * Shows the form.
	 *
	 * @param  string  $mode
	 * @param  int  $id
	 * @return mixed
	 */
	protected function showForm($mode, $id = null)
	{
		// Do we have a page identifier?
		if (isset($id))
		{
			if ( ! $page = $this->pages->find($id))
			{
				$message = Lang::get('platform/pages::message.not_found', compact('id'));

				return Redirect::toAdmin('pages')->withErrors($message);
			}
		}
		else
		{
			$page = $this->pages->createModel();
		}

		// Find this page menu
		if ( ! $menu = $this->menus->findWhere('page_id', (int) $page->id))
		{
			$menu = $this->menus->createModel();
		}

		// Get all the available user roles
		$roles = $this->roles->findAll();

		// Get all the available templates
		$templates = $this->pages->templates();

		// Get all the available page files
		$files = $this->pages->files();

		// Get the root items
		$menus = $this->menus->findAllRoot();

		// Show the page
		return View::make('platform/pages::form', compact(
			'mode', 'page', 'roles', 'templates', 'files', 'menus', 'menu'
		));
	}

	/**
	 * Processes the form.
	 *
	 * @param  string  $mode
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function processForm($mode, $id = null)
	{
		// Check in case the roles input is disabled but we had roles
		// that were previously selected, we want to remove those.
		Input::merge(['roles' => Input::get('roles', [])]);

		// Get the input data
		$data = Input::all();

		// Do we have a page identifier?
		if ($id)
		{
			// Check if the data is valid
			$messages = $this->pages->validForUpdate($id, $data);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Update the page
				$page = $this->pages->update($id, $data);
			}
		}
		else
		{
			// Check if the data is valid
			$messages = $this->pages->validForCreation($data);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Create the pages
				$page = $this->pages->create($data);
			}
		}

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			// Prepare the success message
			$message = Lang::get("platform/pages::message.success.{$mode}");

			return Redirect::toAdmin("pages/{$page->id}/edit")->withSuccess($message);
		}

		return Redirect::back()->withInput()->withErrors($messages);
	}

}
