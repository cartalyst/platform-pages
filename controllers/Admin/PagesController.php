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
use Platform\Content\Controllers\Admin\ContentController;
use Platform\Pages\Models\Page;
use Redirect;
use Symfony\Component\Finder\Finder;
use Theme;
use View;

class PagesController extends ContentController {

	/**
	 * Pages management main page.
	 *
	 * @return mixed
	 */
	public function getIndex()
	{
		// Set the current active menu
		set_active_menu('admin-pages');

		try
		{
			// Get the pages list
			$response = API::get('pages');
			$pages    = $response['pages'];
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the admin dashboard
			return Redirect::toAdmin('');
		}

		// Show the page
		return View::make('platform/pages::index', compact('pages'));
	}

	/**
	 * Datasource for the pages Data Grid.
	 *
	 * @return Cartalyst\DataGrid\DataGrid
	 */
	public function getGrid()
	{
		return DataGrid::make(with(new Page)->newQuery(), array(
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
		// Set the current active menu
		set_active_menu('admin-pages');

		try
		{
			// Get all the available user groups
			$response = API::get('users/groups');
			$groups   = $response['groups'];
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the pages management page
			return Redirect::toAdmin('pages');
		}

		$visibilities = $this->getVisibilities();
		$types        = $this->getTypes();
		$templates    = $this->getSources();
		$files        = $this->getSources();

		// Show the page
		return View::make('platform/pages::create', compact('types', 'visibilities', 'groups', 'templates', 'files'));
	}

	/**
	 * Create a new page form processing.
	 *
	 * @return Redirect
	 */
	public function postCreate()
	{
		return $this->postEdit();
	}

	/**
	 * Page update.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function getEdit($id = null)
	{
		// Set the current active menu
		set_active_menu('admin-pages');

		try
		{
			// Get the page information
			$response = API::get("pages/$id");
			$page     = $response['page'];

			// Get all the available user groups
			$response = API::get('users/groups');
			$groups   = $response['groups'];
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$notifications = with(new Bag)->add('error', $e->getMessage());

			// Return to the pages management page
			return Redirect::toAdmin('pages')->with('notifications', $notifications);
		}

		$visibilities = $this->getVisibilities();
		$types        = $this->getTypes();
		$templates    = $this->getSources();
		$files        = $this->getSources();

		// Show the page
		return View::make('platform/pages::edit', compact('page', 'types', 'visibilities', 'groups', 'templates', 'files'));
	}

	/**
	 * Page update form processing.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function postEdit($id = null)
	{
		try
		{
			// Are we creating a new page?
			if (is_null($id))
			{
				$response = API::post('pages', Input::all());
				$id = $response['page']->id;

				// Prepare the success message
				$success = Lang::get('platform/pages::message.create.success');
			}

			// No, we are updating an existing page
			else
			{
				API::put("pages/$id", Input::all());

				// Prepare the success message
				$success = Lang::get('platform/pages::message.update.success');
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
			API::delete("pages/$id");

			// Set the success message
			$notifications = with(new Bag)->add('success', Lang::get('platform/pages::message.delete.success'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$notifications = with(new Bag)->add('error', Lang::get('platform/pages::message.delete.error'));
		}

		// Redirect to the pages management page
		return Redirect::toAdmin('pages')->with('messages', $notifications);
	}

	/**
	 * Page clone.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function getClone($id = null)
	{
		try
		{
			// Get the page information
			$response = API::get("pages/$id");
			$page     = $response['page'];

			// Get all the available user groups
			$response = API::get('users/groups', array('organized' => true));
			$groups   = $response['groups'];
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Return to the page management page
			return Redirect::toAdmin('pages');
		}

		$visibilities = $this->getVisibilities();
		$types        = $this->getTypes();
		$templates    = $this->getSources();
		$files        = $this->getSources();

		// Show the page
		return View::make('platform/pages::clone', compact('page', 'types', 'visibilities', 'groups', 'templates', 'files'));
	}

	/**
	 * Page clone form processing.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function postClone($id = null)
	{
		return $this->postEdit();
	}

	protected function getVisibilities()
	{
		return array(
			'always'    => 'Shown Always',
			'logged_in' => 'Logged In Only',
		);
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
