<?php namespace Platform\Pages\Controllers\Api\V1;
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

use League\Fractal\Manager;
use Platform\Access\Controllers\ApiController;
use Platform\Pages\Transformers\PageTransformer;
use Platform\Pages\Repositories\PageRepositoryInterface;

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
		// Create the page
		list($messages, $page) = $this->pages->create(request()->json()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
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

		$message = trans('platform/pages::message.not_found', compact('id'));

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
		// Update the page
		list($messages, $page) = $this->pages->update($id, request()->json()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
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

		$message = trans('platform/pages::message.not_found', compact('id'));

		return $this->responseWithErrors($message, 404);
	}

}
