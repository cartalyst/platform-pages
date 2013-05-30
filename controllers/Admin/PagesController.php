<?php namespace Platform\Pages\Controllers\Admin;
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

use API;
use Cartalyst\Api\Http\ApiHttpException;
use DataGrid;
use Illuminate\Support\MessageBag as Bag;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Platform\Pages\Models\Page;
use Redirect;
use Symfony\Component\Finder\Finder;
use Theme;
use View;

class PagesController extends AdminController {

	/**
	 * Pages management main page.
	 *
	 * @return mixed
	 */
	public function getIndex()
	{
		// Set the current active menu
		set_active_menu('admin-pages');

		// Show the page
		return View::make('platform/pages::index');
	}

	/**
	 * Datasource for the pages Data Grid.
	 *
	 * @return Cartalyst\DataGrid\DataGrid
	 */
	public function getGrid()
	{
		// Get the content list
		$response = API::get('v1/pages');

		//
		return DataGrid::make($response['pages'], array(
			'id',
			'name',
			'slug',
			'enabled',
			'created_at',
		));
	}

	/**
	 * Create a new page.
	 *
	 * @return mixed
	 */
	public function getCreate()
	{
		return $this->showForm(null, 'create');
	}

	/**
	 * Create a new page form processing.
	 *
	 * @return Redirect
	 */
	public function postCreate()
	{
		return $this->processForm();
	}

	/**
	 * Page update.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function getEdit($id = null)
	{
		return $this->showForm($id, 'update');
	}

	/**
	 * Page update form processing.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function postEdit($id = null)
	{
		return $this->processForm($id);
	}

	/**
	 * Page copy.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function getCopy($id = null)
	{
		return $this->showForm($id, 'copy');
	}

	/**
	 * Page copy form processing.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function postCopy($id = null)
	{
		return $this->processForm($id);
	}

	/**
	 * Page delete.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function getDelete($id = null)
	{
		try
		{
			// Delete the page
			API::delete("v1/pages/$id");

			// Set the success message
			$notifications = with(new Bag)->add('success', Lang::get('platform/pages::message.success.delete'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$notifications = with(new Bag)->add('error', Lang::get('platform/pages::message.error.delete'));
		}

		// Redirect to the pages management page
		return Redirect::toAdmin('pages')->with('notifications', $notifications);
	}

	/**
	 * Shows the form.
	 *
	 * @param  mixed   $id
	 * @param  string  $page
	 * @return mixed
	 */
	protected function showForm($id = null, $segment = null)
	{
		try
		{
			// Set the current active menu
			set_active_menu('admin-pages');

			// Data fallback
			$page       = null;
			$pageGroups = array();

			// Do we have a page identifier?
			if ( ! is_null($id))
			{
				// Get the page information
				$response = API::get("v1/pages/$id");
				$page     = $response['page'];

				// Get this page groups
				$pageGroups = $page->groups()->lists('name', 'group_id');
			}

			// Get all the available user groups
			$response = API::get('v1/users/groups');
			$groups   = $response['groups'];

			// Get all the available templates
			$templates = $this->getSources();

			// Get all the available page files
			$files = $this->getSources();

			// Show the page
			return View::make('platform/pages::form', compact('page', 'segment', 'groups', 'pageGroups', 'templates', 'files'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$notifications = with(new Bag)->add('error', $e->getMessage());

			// Return to the pages management page
			return Redirect::toAdmin('pages')->with('notifications', $notifications);
		}
	}

	/**
	 * Processes the form.
	 *
	 * @param  mixed  $id
	 * @return Redirect
	 */
	protected function processForm($id = null)
	{
		try
		{
			// Are we creating a new page?
			if (is_null($id))
			{
				// Make the request
				$response  = API::post('v1/pages', Input::all());
				$id        = $response['page']->id;

				// Prepare the success message
				$success = Lang::get('platform/pages::message.success.create');
			}

			// No, we are updating an page content
			else
			{
				// Make the request
				API::put("v1/pages/$id", Input::all());

				// Prepare the success message
				$success = Lang::get('platform/pages::message.success.update');
			}

			// Set the success message
			$notifications = with(new Bag)->add('success', $success);

			// Redirect to the page edit page
			return Redirect::toAdmin("pages/edit/$id")->with('notifications', $notifications);
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the appropriate page
			return Redirect::back()->withInput()->withErrors($e->getErrors());
		}
	}



	protected function getSources()
	{
		$theme = Page::getTheme();
		$extensions = array_keys(View::getExtensions());

		$finder = new Finder;
		$finder
			->in(Theme::getCascadedViewPaths($theme))
			->depth('< 3')
			->name(sprintf(
				'/.*?\.(%s)/',
				implode('|', array_map(function($extension)
				{
					return preg_quote($extension, '/');
				}, $extensions))
			));

		$files = array();

		// Replace all file extensions with nothing. pathinfo()
		// won't tackle ".blade.php" so this is our best shot.
		$replacements = array_pad(array(), count($extensions), '');

		foreach ($finder->files() as $file)
		{
			$_file = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());

			// Because we want to save a valid source for the view
			// loader, we simply want to store the view name as if
			// the view loader was loading it.
			$files[rtrim(str_replace($extensions, $replacements, $_file), '.')] = $_file;
		}

		return $files;
	}

}
