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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Pages\Models\Page;

interface PageRepositoryInterface {

	/**
	 * Returns a dataset compatible with data grid.
	 *
	 * @return \Platform\Pages\Models\Page
	 */
	public function grid();

	/**
	 * Returns all the page entries.
	 *
	 * @return \Platform\Pages\Models\Page
	 */
	public function findAll();

	/**
	 * Returns all the enabled page entries.
	 *
	 * @return \Platform\Pages\Models\Page
	 */
	public function findAllEnabled();

	/**
	 * Returns a page by its primary key.
	 *
	 * @param  int  $id
	 * @return \Platform\Pages\Models\Page
	 */
	public function find($id);

	/**
	 * Returns a page by its primary key that is enabled.
	 *
	 * @param  int  $id
	 * @return \Platform\Pages\Models\Page
	 */
	public function findEnabled($id);

	/**
	 * Determines if the given page is valid for creation.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForCreation(array $data);

	/**
	 * Determines if the given page is valid for updating.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForUpdate($id, array $data);

	/**
	 * Creates a page with the given data.
	 *
	 * @param  array  $data
	 * @return \Cartalyst\Pages\Models\Page
	 */
	public function create(array $data);

	/**
	 * Updates a page with the given data.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Cartalyst\Pages\Models\Page
	 */
	public function update($id, array $data);

	/**
	 * Deletes the given page.
	 *
	 * @param  int  $id
	 * @return bool
	 */
	public function delete($id);

	/**
	 * Enables the given page.
	 *
	 * @param  int  $id
	 * @return \Cartalyst\Pages\Models\Page
	 */
	public function enable($id);

	/**
	 * Disables the given page.
	 *
	 * @param  int  $id
	 * @return \Cartalyst\Pages\Models\Page
	 */
	public function disable($id);

	/**
	 * Renders the page.
	 *
	 * @param  \Platform\Pages\Models\Page  $page
	 * @return string
	 * @throws \RuntimeException
	 */
	public function render(Page $page);

}
