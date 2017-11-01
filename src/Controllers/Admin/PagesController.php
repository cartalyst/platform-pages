<?php

/**
 * Part of the Platform Pages extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Pages extension
 * @version    6.0.6
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Pages\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Cartalyst\DataGrid\Export\Providers\ExportProvider;
use Platform\Pages\Repositories\PageRepositoryInterface;

class PagesController extends AdminController
{
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
        // Get a list of all the available tags
        $tags = $this->pages->getAllTags();

        return view('platform/pages::index', compact('tags'));
    }

    /**
     * Datasource for the pages Data Grid.
     *
     * @return \Cartalyst\DataGrid\DataGrid
     */
    public function grid()
    {
        $settings = [
            'columns' => [
                'id',
                'name',
                'slug',
                'uri',
                'enabled',
                'created_at',
            ],
            'sorts' => [
                'column'    => 'created_at',
                'direction' => 'desc',
            ],
            'transformer' => function ($element) {
                $element->edit_uri = route('admin.pages.edit', $element->id);

                return $element;
            },
        ];

        $provider = new ExportProvider();

        return datagrid($this->pages->grid(), $settings, $provider);
    }

    /**
     * Show the form for creating a new page.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
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
        $type = $this->pages->delete($id) ? 'success' : 'error';

        $this->alerts->{$type}(
            trans("platform/pages::message.{$type}.delete")
        );

        return redirect()->route('admin.pages.all');
    }

    /**
     * Executes the mass action.
     *
     * @return \Illuminate\Http\Response
     */
    public function executeAction()
    {
        $action = request()->input('action');

        if (in_array($action, $this->actions)) {
            foreach (request()->input('rows', []) as $row) {
                $this->pages->{$action}($row);
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function showForm($mode, $id = null)
    {
        if (! $data = $this->pages->getPreparedPage($id)) {
            $this->alerts->error(trans('platform/pages::message.not_found', compact('id')));

            return redirect()->toAdmin('pages');
        }

        $page = $data['page'];

        $menu = $data['menu'];

        $files = $data['files'];

        $menus = $data['menus'];

        $roles = $data['roles'];

        $templates = $data['templates'];

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
        list($messages) = $this->pages->store($id, request()->all());

        // Do we have any errors?
        if ($messages->isEmpty()) {
            $this->alerts->success(trans("platform/pages::message.success.{$mode}"));

            return redirect()->route('admin.pages.all');
        }

        $this->alerts->error($messages, 'form');

        return redirect()->back()->withInput();
    }
}
