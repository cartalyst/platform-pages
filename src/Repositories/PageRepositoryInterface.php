<?php namespace Platform\Pages\Repositories;
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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Themes\ThemeBag;
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
	 * @return \Illuminate\Support\Collection
	 */
	public function findAll();

	/**
	 * Returns all the enabled page entries.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function findAllEnabled();

	/**
	 * Returns a page by its primary key, slug or uri.
	 *
	 * @param  mixed  $id
	 * @return \Platform\Pages\Models\Page
	 */
	public function find($id);

	/**
	 * Returns a page by its slug.
	 *
	 * @param  string  $slug
	 * @return \Platform\Pages\Models\Page
	 */
	public function findBySlug($slug);

	/**
	 * Returns a page by its uri.
	 *
	 * @param  string  $uri
	 * @return \Platform\Pages\Models\Page
	 */
	public function findByUri($uri);

	/**
	 * Returns a page by its primary key that is enabled.
	 *
	 * @param  int  $id
	 * @return \Platform\Pages\Models\Page
	 */
	public function findEnabled($id);

	/**
	 * Returns the given page with the necessary and
	 * belonging data already prepared to be used.
	 *
	 * @param  int  $id
	 * @return array
	 */
	public function getPreparedPage($id);

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
	 * @param  \Platform\Pages\Models\Page  $page
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForUpdate(Page $page, array $data);

	/**
	 * Creates a page with the given data.
	 *
	 * @param  array  $data
	 * @return bool|array
	 */
	public function create(array $data);

	/**
	 * Updates a page with the given data.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return bool|array
	 */
	public function update($id, array $data);

	/**
	 * Creates or updates the given page.
	 *
	 * @param  int  $id
	 * @param  array  $input
	 * @return array
	 */
	public function store($id, array $input);

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
	 * @return bool|array
	 */
	public function enable($id);

	/**
	 * Disables the given page.
	 *
	 * @param  int  $id
	 * @return bool|array
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

	/**
	 * Returns a list of the available page files on the current active theme.
	 *
	 * @return array
	 */
	public function files();

	/**
	 * Returns a list of the available templates of the current active theme.
	 *
	 * @return array
	 */
	public function templates();

	/**
	 * Returns the theme bag instance.
	 *
	 * @return \Cartalyst\Themes\ThemeBag
	 */
	public function getThemeBag();

	/**
	 * Returns the theme name.
	 *
	 * @return string
	 */
	public function getTheme();

	/**
	 * Returns the finder instance.
	 *
	 * @return \Symfony\Component\Finder\Finder
	 */
	public function getFinder();

	/**
	 * Returns the files paths.
	 *
	 * @return array
	 */
	public function getFilePaths();

	/**
	 * Returns the template paths.
	 *
	 * @return array
	 */
	public function getTemplatePaths();

}
