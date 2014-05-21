<?php namespace Platform\Pages\Repositories;
/**
 * Part of the Platform package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform
 * @version    2.0.0
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
	 * @return \Platform\Pages\Models\Page
	 */
	public function create(array $data);

	/**
	 * Updates a page with the given data.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Platform\Pages\Models\Page
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
	 * @return \Platform\Pages\Models\Page
	 */
	public function enable($id);

	/**
	 * Disables the given page.
	 *
	 * @param  int  $id
	 * @return \Platform\Pages\Models\Page
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
	 * Set the theme bag instance.
	 *
	 * @param  \Cartalyst\Themes\ThemeBag  $themeBag
	 * @return void
	 */
	public function setThemeBag(ThemeBag $themeBag);

	/**
	 * Returns the theme name.
	 *
	 * @return string
	 */
	public function getTheme();

	/**
	 * Set the theme name.
	 *
	 * @param  string  $theme
	 * @return void
	 */
	public function setTheme($theme);

	/**
	 * Returns the group model.
	 *
	 * @return string
	 */
	public function getGroupModel();

	/**
	 * Set the group model.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function setGroupModel($model);

	/**
	 * Returns the menu model.
	 *
	 * @return string
	 */
	public function getMenuModel();

	/**
	 * Set the menu model.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function setMenuModel($model);

}
