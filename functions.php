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
 * @todo   ALL!
 */
function pagesFindTemplates()
{
	return array();
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
