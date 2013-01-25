<?php
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

/**
 * Return all the available frontend themes.
 *
 * @return array
 * @todo   REFACTOR!
 */
function pagesFindTemplates()
{
	// Get both active and fallback themes
	// ### change this aswelllllll
	$themes = array(
		'active'   => 'fancy',
		'fallback' => 'default'
	);

	// Empty array to store themes layouts
	$templates = array();

	// Loop through the themes
	foreach ($themes as $themeStatus => $themeName)
	{
		// clean this ....................
		$templatePath = app('path') . '/../public/platform/themes/public/platform/' . $themeName . '/views/templates/*.blade.php';

		$layouts = glob($templatePath);

		// Loop through this theme layout files
		foreach ($layouts as $layout)
		{
			// Get the layout file base name
			$layout = str_replace('.blade.php', '', basename($layout));

			// Prevent duplicates because we use overriding
			if ( ! array_key_exists($layout, $templates))
			{
				$templates[ $themeName ][ $layout ] = $layout;
			}
		}
	}

	return $templates;
}

/**
 * Returns all the valid pages visibility statuses.
 *
 * @return array
 * @todo   Add Localisation!
 */
function pagesVisibilityStatuses()
{
	return array(
		0 => 'Show Always',
		1 => 'Logged In'
	);
}

/**
 * Returns all the valid pages storage types.
 *
 * @return array
 * @todo   Add Localisation!
 */
function pagesStorageTypes()
{
	return array(
		'db'   => 'Database',
		'file' => 'File'
	);
}



function findPageFiles()
	{
		// Find current active and fallback themes for the frontend;
		//
		$themes['active'] = Platform::get('platform/themes::theme.frontend');

		// Set the fallback if the theme is not on default
		//
		if ($themes['active'] != 'default')
		{
			$themes['fallback'] = 'default';
		}

		$files = array();
		$fileNames = array();
		foreach ($themes as $theme => $name)
		{
			$path = path('public') . 'platform/themes/frontend/'.$name.'/extensions/platform/pages/files';

			$_files = glob($path.DS.'*.blade.php');

			foreach ($_files as $file)
			{
				$file = str_replace('.blade.php', '', basename($file));

				// prevent duplicates because we use overriding
				if ( ! in_array($file, $fileNames))
				{
					$files[$name][$file] = $file;
					$fileNames[] = $file;
				}
			}

		}

		return $files;
	}
