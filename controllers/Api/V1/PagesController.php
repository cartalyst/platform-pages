<?php namespace Platform\Pages\Controllers\Api\V1;
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

use Platform\Routing\Controllers\ApiController;
use Platform\Pages\Page;

class PagesController extends ApiController {

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $validationRules = array(
		'name'       => 'required',
		'slug'       => 'required|unique:pages,slug',
		'status'     => 'required',
		'type'       => 'required',
		'template'   => 'required',
		'visibility' => 'required',
		'value'      => 'required'
	);

	/**
	 *
	 *
	 * @var Platform\Pages\Page
	 */
	protected $model;

	/**
	 * Initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$app = app();

		$this->model = $app->make('platform/pages::page');
	}

	/**
	 * Display a listing of pages using the given filters.
	 *
	 * @return Cartalyst\Api\Http\Response
	 * @todo   Refactor to allow search filters !!
	 */
	public function index()
	{
		if ( ! $limit = $this->input('limit'))
		{
			return $this->response(array('pages' => $this->model->all()));
		}

		return $this->response(array('pages' => $this->model->paginate($limit)));
	}

	/**
	 * Create page.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function create()
	{
		// Validate the data
		$validator = \Validator::make(\Input::all(), $this->validationRules);

		// Check if the validation passed
		if ($validator->passes())
		{
			// Create the page
			$page = new Page;

			//
			$page->name       = \Input::get('name');
			$page->slug       = \Input::get('slug'); # we need to make sure this is a slug!
			$page->template   = \Input::get('template');
			$page->type       = \Input::get('type');
			$page->visibility = \Input::get('visibility');
			$page->groups     = json_encode(\Input::get('groups'));
			$page->value      = \Input::get('value');
			$page->status     = \Input::get('status');

			// Was the page created?
			if ($page->save())
			{
				// Page created with success
				return $this->response(array(
					'message' => \Lang::get('platform/pages::messages.create.success')
				));
			}

			// There was a problem creating the page
			return $this->response(array(
				'message' => \Lang::get('platform/pages::messages.create.error')
			), 500);
		}

		// Return the validator object
		return $this->response(compact('validator'));
	}

	/**
	 * Returns information about the given page.
	 *
	 * @param  int  $pageId
	 * @return Cartalyst\Api\Http\Response
	 */
	public function show($pageId)
	{
		// Do we have the page slug?
		if ( ! is_numeric($pageId))
		{
			$page = $this->model->where('slug', '=', $pageId);
		}

		// We must have the page id
		else
		{
			$page = $this->model->where('id', '=', $pageId);
		}

		// Do we only want the enabled page ?
		if ($status = $this->input('enabled'))
		{
			$page->where('status', '=', 1);
		}

		// Check if the page exists
		if ( ! is_null($page = $page->first()))
		{
			return $this->response(compact('page'));
		}

		// Page does not exist
		return $this->response(array(
			'message' => \Lang::get('platform/pages::messages.does_not_exist', compact('pageId'))
		), 404);
	}

	/**
	 * Updates the given page.
	 *
	 * @param  int  $pageId
	 * @return Cartalyst\Api\Http\Response
	 */
	public function update($pageId)
	{
		// Check if the page exists
		if(is_null($page = $this->model->find($pageId)))
		{
			return $this->response(array(
				'message' => \Lang::get('platform/pages::messages.does_not_exist', compact('pageId'))
			), 404);
		}

		// Update the validation rules, so it ignores the current
		// page slug.
		$this->validationRules['slug'] = 'required|unique:pages,slug,' . $page->slug . ',slug';

		// Validate the data
		$validator = \Validator::make(\Input::all(), $this->validationRules);

		// Check if the validation passed
		if ($validator->passes())
		{
			// Update the page data
			$page->name       = \Input::get('name');
			$page->slug       = \Input::get('slug'); # we need to make sure this is a slug!
			$page->template   = \Input::get('template');
			$page->type       = \Input::get('type');
			$page->visibility = \Input::get('visibility');
			$page->groups     = json_encode(\Input::get('groups'));
			$page->value      = \Input::get('value');
			$page->status     = \Input::get('status');

			// Was the page updated?
			if ($page->save())
			{
				return $this->response(array(
					'message' => \Lang::get('platform/pages::messages.update.success')
				));
			}

			// There was a problem updating the page
			return $this->response(array(
				'message' => \Lang::get('platform/pages::messages.update.error')
			), 500);
		}

		// Return the validator object
		return $this->response(compact('validator'));
	}

	/**
	 * Deletes the given page.
	 *
	 * @param  int  $pageId
	 * @return Cartalyst\Api\Http\Response
	 */
	public function destroy($pageId)
	{
		// Check if the page exists
		if (is_null($page = $this->model->find($pageId)))
		{
			return $this->response(array(
				'message' => \Lang::get('platform/pages::messages.does_not_exist', compact('pageId'))
			), 404);
		}

		// Was the page deleted?
		if ($page->delete())
		{
			return $this->response(array(
				'message' => \Lang::get('platform/pages::messages.delete.success')
			));
		}

		// There was a problem deleting the page
		return $this->response(array(
			'message' => \Lang::get('platform/pages::messages.delete.error')
		));
	}

}
