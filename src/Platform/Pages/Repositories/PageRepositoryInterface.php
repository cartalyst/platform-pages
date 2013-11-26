<?php namespace Platform\Pages\Repositories;
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

interface PageRepositoryInterface {

	/**
	 * Return a dataset compatible with the data grid.
	 *
	 * @return mixed
	 */
	public function grid();

	/**
	 * Return all the page entries.
	 *
	 * @return \Platform\Pages\Page
	 */
	public function findAll();

	/**
	 * Get an page by it's primary key.
	 *
	 * @param  int  $id
	 * @return \Platform\Pages\Page
	 */
	public function find($id);

	public function findEnabled($id);

	/**
	 * Determine if the given page is valid for creation.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForCreation(array $data);

	/**
	 * Determine if the given page is valid for updating.
	 *
	 * @param  int    $id
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForUpdate($id, array $data);

	/**
	 * Creates a page with the given data.
	 *
	 * @param  array  $data
	 * @return \Cartalyst\Pages\Page
	 */
	public function create(array $data);

	/**
	 * Updates a page with the given data.
	 *
	 * @param  int    $id
	 * @param  array  $data
	 * @return \Cartalyst\Pages\Page
	 */
	public function update($id, array $data);

	/**
	 * Deletes the given page.
	 *
	 * @param  int  $id
	 * @return bool|null
	 */
	public function delete($id);

}
