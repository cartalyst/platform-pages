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
	 * Display a listing of pages.
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
		// Get all the pages
		$response = API::get('v1/pages');

		// Return the Data Grid object
		return DataGrid::make($response['pages'], array(
			'id',
			'name',
			'slug',
			'enabled',
			'created_at',
		));
	}

	/**
	 * Show the form for creating a new page.
	 *
	 * @return mixed
	 */
	public function getCreate()
	{
		return $this->showForm(null, 'create');
	}

	/**
	 * Handle posting of the form for creating a new page.
	 *
	 * @return Redirect
	 */
	public function postCreate()
	{
		return $this->processForm();
	}

	/**
	 * Show the form for updating a page.
	 *
	 * @param  mixed  $slug
	 * @return mixed
	 */
	public function getEdit($slug = null)
	{
		return $this->showForm($slug, 'update');
	}

	/**
	 * Handle posting of the form for updating a page.
	 *
	 * @param  mixed  $slug
	 * @return Redirect
	 */
	public function postEdit($slug = null)
	{
		return $this->processForm($slug);
	}

	/**
	 * Show the form for copying a page.
	 *
	 * @param  mixed  $slug
	 * @return mixed
	 */
	public function getCopy($slug = null)
	{
		return $this->showForm($slug, 'copy');
	}

	/**
	 * Handle posting of the form for copying a page.
	 *
	 * @return Redirect
	 */
	public function postCopy()
	{
		return $this->processForm();
	}

	/**
	 * Remove the specified page.
	 *
	 * @param  mixed  $slug
	 * @return Redirect
	 */
	public function getDelete($slug = null)
	{
		// Instantiate a new message bag
		$bag = new Bag;

		try
		{
			// Delete the page
			API::delete("v1/page/{$slug}");

			// Set the success message
			$bag->add('success', Lang::get('platform/pages::message.success.delete'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$bag->add('error', Lang::get('platform/pages::message.error.delete'));
		}

		// Redirect to the pages management page
		return Redirect::toAdmin('pages')->with('notifications', $bag);
	}

	/**
	 * Shows the form.
	 *
	 * @param  mixed   $slug
	 * @param  string  $pageSegment
	 * @return mixed
	 */
	protected function showForm($slug = null, $pageSegment = null)
	{
		try
		{
			// Set the current active menu
			set_active_menu('admin-pages');

			// Data fallback
			$page       = null;
			$pageGroups = array();

			// Do we have a page identifier?
			if ( ! is_null($slug))
			{
				// Get the page information
				$response = API::get("v1/page/{$slug}");
				$page     = $response['page'];

				// Get this page groups
				$pageGroups = $page->groups->lists('name', 'id');
			}

			// Get all the available user groups
			$response = API::get('v1/users/groups');
			$groups   = $response['groups'];

			// Get all the available templates
			$templates = $this->getTemplates();

			// Get all the available page files
			$files = $this->getPageFiles();

			// Show the page
			return View::make('platform/pages::form', compact('page', 'groups', 'pageGroups', 'templates', 'files', 'pageSegment'));
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
	 * @param  mixed  $slug
	 * @return Redirect
	 */
	protected function processForm($slug = null)
	{
		try
		{
			// Instantiate a new message bag
			$bag = new Bag;

			// Are we creating a new page?
			if (is_null($slug))
			{
				// Make the request
				$response = API::post('v1/pages', Input::all());
				$slug     = $response['page']->slug;

				// Set the success message
				$bag->add('success', Lang::get('platform/pages::message.success.create'));
			}

			// No, we are updating an page content
			else
			{
				// Make the request
				$response = API::put("v1/pages/{$slug}", Input::all());
				$slug     = $response['page']->slug;

				// Set the success message
				$bag->add('success', Lang::get('platform/pages::message.success.update'));
			}

			// Redirect to the page edit page
			return Redirect::toAdmin("pages/edit/{$slug}")->with('notifications', $bag);
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the appropriate page
			return Redirect::back()->withInput()->withErrors($e->getErrors());
		}
	}

	/**
	 * Returns a list of the available page files on the current active theme.
	 *
	 * @return array
	 */
	protected function getPageFiles()
	{
		$theme = Page::getTheme();

		$extensions = array_keys(View::getExtensions());

		$paths = array();

		## this if shouldn't be required, since we are only interested
		## on the current active theme views
		foreach (Theme::getCascadedViewPaths($theme) as $path)
		{
			if (strpos($path, 'admin') == false)
			{
				$paths[] = $path . DIRECTORY_SEPARATOR . 'pages';
			}
		}
		##

		$finder = new Finder;
		$finder->in($paths);

		$files = array();

		// Replace all file extensions with nothing. pathinfo()
		// won't tackle ".blade.php" so this is our best shot.
		$replacements = array_pad(array(), count($extensions), '');

		foreach ($finder->files() as $file)
		{
			$file = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());

			// Because we want to save a valid source for the view loader, we
			// simply want to store the view name as if the view loader was
			// loading it.
			$files[rtrim(str_replace($extensions, $replacements, $file), '.')] = $file;
		}

		return $files;
	}


	/**
	 * Returns a list of the available templates of the current active theme.
	 *
	 * @return array
	 */
	protected function getTemplates()
	{
		$theme = Page::getTheme();

		$extensions = array_keys(View::getExtensions());

		$finder = new Finder;
		$finder->in(Theme::getCascadedViewPaths($theme));
		$finder->depth('< 3');
		$finder->name(sprintf(
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
			$file = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());

			// Because we want to save a valid source for the view loader, we
			// simply want to store the view name as if the view loader was
			// loading it.
			$files[rtrim(str_replace($extensions, $replacements, $file), '.')] = $file;
		}

		return $files;
	}

}
