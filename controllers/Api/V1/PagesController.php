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
use Str;
use Validator;

class PagesController extends ApiController {

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $validationRules = array(
		'name'       			=> 'required',
		'slug'       			=> 'required|unique:pages,slug',
		'uri'        			=> 'required|unique:pages,uri',
		'enabled'    			=> 'required',
		'type'       			=> 'required|in:database,filesystem',
		'visibility' 			=> 'required|in:always,logged_in,admin',

		// Database page
		'template'   			=> 'required_if:type,database',
		// 'section'    			=> 'required_if:type,database',
		// 'value'      			=> 'required_if:type,database',

		// Filesystem page
		'file'       			=> 'required_if:type,filesystem',
	);

	/**
	 * Holds the pages model.
	 *
	 * @var \Platform\Pages\Models\Page
	 */
	protected $model;

	/**
	 * Initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->model = app('Platform\Pages\Models\Page');
	}

	/**
	 * Display a listing of the pages.
	 *
	 * @return \Cartalyst\Api\Http\Response
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
	 * Store a newly created page in storage.
	 *
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function create()
	{
		// Create a new validator instance from our dynamic rules
		$validator = Validator::make(Input::all(), $this->validationRules);

		// If validation fails, we'll exit the operation now
		if ($validator->fails())
		{
			return Response::api(array('errors' => $validator->errors()), 422);
		}

		// Was the page created?
		if ($page = $this->model->create(Input::all()))
		{
			// Loop through the groups
			foreach (Input::get('groups', array()) as $id)
			{
				$group = Sentry::getGroupProvider()->findById($id);

				$page->groups()->attach($group);
			}

			// Page successfully created
			return Response::api(compact('page'));
		}

		// There was a problem creating the page
		return Response::api(Lang::get('platform/pages::message.error.create'), 500);
	}

	/**
	 * Display the specified page.
	 *
	 * @param  mixed  $id
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function show($id = null)
	{
		// Get a new query builder
		$query = $this->model->newQuery()->with('groups');

		// Search for the page uri, slug or id
		$query->orWhere(function($query) use ($id)
		{
			$query->where('uri', $id);
			$query->orWhere('slug', $id);
			$query->orWhere('id', 'LIKE', $id);
		});

		// Search for page by it's status
		if ($status = Input::get('enabled'))
		{
			$query->where('enabled', (int) $status);
		}

		// Grab the page
		if ( ! $page = $query->first())
		{
			return Response::api(Lang::get('platform/pages::message.not_found', compact('id')), 404);
		}

		return Response::api(compact('page'));
	}

	/**
	 * Update the specified page in storage.
	 *
	 * @param  mixed  $id
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function update($id = null)
	{
		// Get a new query builder
		$query = $this->model->newQuery();

		// Search for the page uri, slug or id
		$query->orWhere(function($query) use ($id)
		{
			$query->where('uri', $id);
			$query->orWhere('slug', $id);
			$query->orWhere('id', 'LIKE', $id);
		});

		// Grab the page
		if ( ! $page = $query->first())
		{
			return Response::api(Lang::get('platform/pages::message.not_found', compact('id')), 404);
		}

		// Update the validation rules
		$this->validationRules['slug'] = "required|unique:pages,slug,{$page->slug},slug";
		$this->validationRules['uri'] = "required|unique:pages,uri,{$page->uri},uri";

		// Create a new validator instance from our dynamic rules
		$validator = Validator::make(Input::all(), $this->validationRules);

		// If validation fails, we'll exit the operation now
		if ($validator->fails())
		{
			return Response::api(array('errors' => $validator->errors()), 422);
		}

		// Was the page updated?
		if ( ! $page->fill(Input::all())->save())
		{
			// There was a problem updating the page
			return Response::api(Lang::get('platform/pages::message.error.edit'), 500);
		}

		// Get the current page groups
		$pageGroups = $page->groups->lists('id');

		// Get the selected groups
		$selectedGroups = Input::get('groups', array());

		// Groups comparison between the groups the page currently
		// have and the groups the page will have.
		$groupsToAdd    = array_diff($selectedGroups, $pageGroups);
		$groupsToRemove = array_diff($pageGroups, $selectedGroups);

		// Assign the group to the page
		foreach ($groupsToAdd as $id)
		{
			$group = Sentry::getGroupProvider()->findById($id);

			$page->groups()->attach($group);
		}

		// Remove the group from the page
		foreach ($groupsToRemove as $id)
		{
			$group = Sentry::getGroupProvider()->findById($id);

			$page->groups()->detach($group);
		}

		return Response::api(compact('page'));
	}

	/**
	 * Remove the specified page from storage.
	 *
	 * @param  mixed  $id
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function destroy($id = null)
	{
		// Get a new query builder
		$query = $this->model->newQuery();

		// Search for the page uri, slug or id
		$query->orWhere(function($query) use ($id)
		{
			$query->where('uri', $id);
			$query->orWhere('slug', $id);
			$query->orWhere('id', 'LIKE', $id);
		});

		// Grab the page
		if ( ! $page = $query->first())
		{
			return Response::api(Lang::get('platform/pages::message.not_found', compact('id')), 404);
		}

		// Delete the page
		$page->delete();

		// Page successfully deleted
		return Response::api(Lang::get('platform/pages::message.success.delete'));
	}

}
