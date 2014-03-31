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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Input;
use Lang;
use League\Fractal\Manager;
use Platform\Pages\Repositories\PageRepositoryInterface;
use Platform\Pages\Transformers\PageTransformer;
use Platform\Foundation\Controllers\ApiController;

class PagesController extends ApiController {

	/**
	 * The Pages repository.
	 *
	 * @var \Platform\Pages\Repositories\PageRepositoryInterface
	 */
	protected $pages;

	/**
	 * The Page transformer.
	 *
	 * @var \Platform\Page\Transformers\PageTransformer
	 */
	protected $transformer;

	/**
	 * Constructor.
	 *
	 * @param  \League\Fractal\Manager  $fractal
	 * @param  \Platform\Pages\Repositories\PageRepositoryInterface  $pages
	 * @return void
	 */
	public function __construct(Manager $fractal, PageRepositoryInterface $pages)
	{
		parent::__construct($fractal);

		$this->pages = $pages;

		$this->transformer = new PageTransformer($pages);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$page = $this->pages->findAll();

		return $this->respondWithCollection($page, $this->transformer);
	}

	/**
	 * Adds a new resource into the storage.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store()
	{
		$data = Input::json()->all();

		$messages = $this->pages->validForCreation($data);

		if ($messages->isEmpty())
		{
			$page = $this->pages->create($data);

			return $this->respondWithItem($page, $this->transformer);
		}

		return $this->responseWithErrors($messages, 422);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if ($page = $this->pages->find($id))
		{
			return $this->respondWithItem($page, $this->transformer);
		}

		$message = Lang::get('platform/pages::message.not_found', compact('id'));

		return $this->responseWithErrors($message, 404);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update($id)
	{
		$data = Input::json()->all();

		$messages = $this->pages->validForUpdate($id, $data);

		if ($messages->isEmpty())
		{
			$page = $this->pages->update($id, $data);

			return $this->respondWithItem($page, $this->transformer);
		}

		return $this->responseWithErrors($messages, 422);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if ($page = $this->pages->find($id))
		{
			$page->delete();

			return $this->responseWithNoContent();
		}

		$message = Lang::get('platform/pages::message.not_found', compact('id'));

		return $this->responseWithErrors($message, 404);
	}

}
