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

use Input;
use Lang;
use Platform\Routing\Controllers\ApiController;
use Platform\Pages\Page;
use Response;
use Validator;

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
		// 'template'   => 'required',
		'visibility' => 'required',
		'value'      => 'required',
	);

	/**
	 * Holds the pages model.
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
	 */
	public function index()
	{
		if ($limit = Input::get('limit'))
		{
			$pages = $this->model->paginate($limit);
		}
		else
		{
			$pages = $this->model->all();
		}

		return Response::api(compact('pages'));
	}

	/**
	 * Create page.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function create()
	{
		// Get all the inputs
		$inputs = Input::all();

		// Create a new validator instance from our dynamic rules
		$validator = Validator::make($inputs, $this->validationRules);

		// If validation fails, we'll exit the operation now
		if($validator->fails())
		{
			return Response::api(array('errors' => $validator), 422);
		}

		// Was the page created?
		if ($page = Page::create($inputs))
		{
			// Page successfully created
			return Response::api(compact('page'));
		}

		// There was a problem creating the content
		return Response::api(Lang::get('platform/pages::message.create.error'), 500);
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
		if (Input::get('enabled'))
		{
			$page->where('status', '=', 1);
		}

		// Check if the page exists
		if ( ! is_null($page = $page->first()))
		{
			return Response::api(compact('page'));
		}

		// Page does not exist
		return Response::api(Lang::get('platform/pages::message.does_not_exist', compact('pageId')), 404);
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
			return Response::api(Lang::get('platform/pages::message.does_not_exist', compact('pageId')), 404);
		}

		// Get all the inputs
		$inputs = Input::all();

		// Update the validation rules, so it ignores the current page slug.
		$this->validationRules['slug'] = 'required|unique:pages,slug,'.$page->slug.',slug';

		// Create a new validator instance from our dynamic rules
		$validator = Validator::make($inputs, $this->validationRules);

		// If validation fails, we'll exit the operation now
		if ($validator->fails())
		{
			return Response::api(array('errors' => $validator), 422);
		}

		// Update the page data
		$page->fill($inputs);

		// Was the page updated?
		if ($page->save())
		{
			return Response::api(compact('page'));
		}

		// There was a problem updating the content
		return Response::api(Lang::get('platform/pages::message.update.error'), 500);
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
			return \Response::api(Lang::get('platform/pages::message.does_not_exist', compact('pageId')), 404);
		}

		// Delete the page
		$page->delete();

		// Page successfully deleted
		return Response::api(Lang::get('platform/pages::message.delete.success'));
	}

}
