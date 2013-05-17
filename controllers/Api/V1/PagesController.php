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
use Response;
use Sentry;
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
		'enabled'    => 'required',
		'type'       => 'required|in:database,filesystem',
		'visibility' => 'required|in:always,logged_in',

		// Database page
		'template'   => 'required_if:type,database',
		'section'    => 'required_if:type,database',
		'value'      => 'required_if:type,database',

		// Filesystem page
		'file'       => 'required_if:type,filesystem',
	);

	/**
	 * Holds the pages model.
	 *
	 * @var Platform\Pages\Models\Page
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
			$pages = $this->model->with('groups')->paginate($limit);
		}
		else
		{
			$pages = $this->model->with('groups')->get();
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
		$input = Input::all();

		// Create a new validator instance from our dynamic rules
		$validator = Validator::make($input, $this->validationRules);

		// If validation fails, we'll exit the operation now
		if ($validator->fails())
		{
			return Response::api(array('errors' => $validator->errors()), 422);
		}

		// Was the page created?
		if ($page = $this->model->create($input))
		{
			//
			foreach (Input::get('groups', array()) as $id)
			{
				$group = Sentry::getGroupProvider()->findById($id);

				$page->groups()->attach($group);
			}

			// Page successfully created
			return Response::api(compact('page'));
		}

		// There was a problem creating the content
		return Response::api(Lang::get('platform/pages::message.error.create'), 500);
	}

	/**
	 * Returns information about the given page.
	 *
	 * @param  mixed  $id
	 * @return Cartalyst\Api\Http\Response
	 */
	public function show($id = null)
	{
		// Do we have the page slug?
		if ( ! is_numeric($id))
		{
			$page = $this->model->with('groups')->where('slug', $id)->first();
		}

		// We must have the page id
		else
		{
			$page = $this->model->with('groups')->where('id', $id)->first();
		}

		// Check if the page exists
		if (is_null($page))
		{
			return Response::api(Lang::get('platform/pages::message.page_not_found', compact('id')), 404);
		}

		// Do we only want the enabled page ?
		if (Input::get('enabled'))
		{
			$page->where('enabled', '=', 1);
		}

		// Check if the page exists
		if ( ! is_null($page))
		{
			return Response::api(compact('page'));
		}

		// Page does not exist
		return Response::api(Lang::get('platform/pages::message.page_not_found', compact('id')), 404);
	}

	/**
	 * Updates the given page.
	 *
	 * @param  mixed  $id
	 * @return Cartalyst\Api\Http\Response
	 */
	public function update($id = null)
	{
		// Do we have the page slug?
		if ( ! is_numeric($id))
		{
			$page = $this->model->where('slug', $id)->first();
		}

		// We must have the page id
		else
		{
			$page = $this->model->where('id', $id)->first();
		}

		// Check if the page exists
		if (is_null($page))
		{
			return Response::api(Lang::get('platform/pages::message.page_not_found', compact('id')), 404);
		}


		// Get all the inputs
		$input = Input::all();

		// Update the validation rules, so it ignores the current page slug.
		$validationRules = $this->validationRules;
		$validationRules['slug'] = 'required|unique:pages,slug,'.$page->slug.',slug';

		// Create a new validator instance from our dynamic rules
		$validator = Validator::make($input, $validationRules);

		// If validation fails, we'll exit the operation now
		if ($validator->fails())
		{
			return Response::api(array('errors' => $validator->errors()), 422);
		}

		// Update the page data
		$page->fill($input);

		// Was the page updated?
		if ($page->save())
		{
			// There was a problem updating the content
			return Response::api(Lang::get('platform/pages::message.update.error'), 500);
		}

		foreach (Input::get('groups', array()) as $id)
		{
			$group = Sentry::getGroupProvider()->findById($id);
			$user->groups()->attach($group);
		}

		return Response::api(compact('page'));
	}

	/**
	 * Deletes the given page.
	 *
	 * @param  mixed  $id
	 * @return Cartalyst\Api\Http\Response
	 */
	public function destroy($id = null)
	{
		// Do we have the page slug?
		if ( ! is_numeric($id))
		{
			$page = $this->model->where('slug', $id)->first();
		}

		// We must have the page id
		else
		{
			$page = $this->model->where('id', $id)->first();
		}

		// Check if the page exists
		if (is_null($page))
		{
			return Response::api(Lang::get('platform/pages::message.page_not_found', compact('id')), 404);
		}

		// Delete the page
		$page->delete();

		// Page successfully deleted
		return Response::api(Lang::get('platform/pages::message.success.delete'));
	}

}
