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

use Platform\Access\Controllers\AdminController;
use Platform\Pages\Repositories\PageRepositoryInterface;

class PagesController extends AdminController {

	/**
	 * The Pages repository.
	 *
	 * @var \Platform\Pages\Repositories\PageRepositoryInterface
	 */
	protected $pages;

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
	 * @param  \Platform\Pages\Repositories\PageRepositoryInterface  $pages
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
	public function index()
	{
		return view('platform/pages::index');
	}

	/**
	 * Datasource for the pages Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
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

		return datagrid($this->pages->grid(), $columns, $settings);
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
			$this->alerts->success(trans('platform/pages::message.success.delete'));

			return redirect()->toAdmin('pages');
		}

		$this->alerts->error(trans('platform/pages::message.error.delete'));

		return redirect()->toAdmin('pages');
	}

	/**
	 * Executes the mass action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function executeAction()
	{
		$action = request()->input('action');

		if (in_array($action, $this->actions))
		{
			foreach (request()->input('entries', []) as $entry)
			{
				$this->pages->{$action}($entry);
			}

			return response('Success');
		}

		return response('Failed', 500);
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
		if ( ! $data = $this->pages->getPreparedPage($id))
		{
			$this->alerts->error(trans('platform/pages::message.not_found', compact('id')));

			return redirect()->toAdmin('pages');
		}

		extract($data);

		return view('platform/pages::form', compact(
			'page', 'roles', 'templates', 'files', 'menus', 'menu', 'mode'
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
		// Store the page
		list($messages, $page) = $this->pages->store($id, request()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			$this->alerts->success(trans("platform/pages::message.success.{$mode}"));

			return redirect()->toAdmin("pages/{$page->id}");
		}

		$this->alerts->error($messages, 'form');

		return redirect()->back()->withInput();
	}

}
