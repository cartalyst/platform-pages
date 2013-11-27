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

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Input;
use Lang;
use Platform\Pages\Repositories\PageRepositoryInterface;
use Platform\Routing\Controllers\ApiController;
use Response;

class PagesController extends ApiController {

	/**
	 * Pages repository.
	 *
	 * @var \Platform\Pages\Repositories\PageRepositoryInterface
	 */
	protected $pages;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Pages\Repositories\PageRepositoryInterface
	 * @return void
	 */
	public function __construct(PageRepositoryInterface $pages)
	{
		parent::__construct();

		$this->pages = $pages;
	}

	/**
	 * Display a listing of the pages.
	 *
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function index()
	{
		$pages = $this->pages->findAll();

		return Response::api(compact('pages'));
	}

	/**
	 * Store a newly created page in storage.
	 *
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function create()
	{
		try
		{
			$input = Input::all();

			$messages = $this->pages->validForCreate($input);

			if ($messages->isEmpty())
			{
				$page = $this->pages->create($input);

				return Response::api(compact('page'));
			}

			return Response::api(array('errors' => $messages), 422);
		}
		catch (ModelNotFoundException $e)
		{
			return Response::api(Lang::get('platform/pages::message.error.create'), 500);
		}
	}

	/**
	 * Display the specified page.
	 *
	 * @param  mixed  $id
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function show($id = null)
	{
		try
		{
			$page = $this->pages->find($id);

			return Response::api(compact('page'));
		}
		catch (ModelNotFoundException $e)
		{
			return Response::api(Lang::get('platform/pages::message.not_found', compact('id')), 404);
		}
	}

	/**
	 * Update the specified page in storage.
	 *
	 * @param  mixed  $id
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function update($id = null)
	{
		try
		{
			$input = Input::all();

			$messages = $this->pages->validForUpdate($id, $input);

			if ($messages->isEmpty())
			{
				$page = $this->pages->update($id, $input);

				return Response::api(compact('page'));
			}

			return Response::api(array('errors' => $messages), 422);
		}
		catch (ModelNotFoundException $e)
		{
			return Response::api(Lang::get('platform/pages::message.not_found', compact('id')), 404);
		}
	}

	/**
	 * Remove the specified page from storage.
	 *
	 * @param  mixed  $id
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function destroy($id = null)
	{
		try
		{
			$this->pages->delete($id);

			return Response::api(Lang::get('platform/pages::message.success.delete'), 204);
		}
		catch (ModelNotFoundException $e)
		{
			return Response::api(Lang::get('platform/pages::message.not_found', compact('id')), 404);
		}
	}

}
