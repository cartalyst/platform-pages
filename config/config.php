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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return array(

	/*
	|--------------------------------------------------------------------------
	| Default Page
	|--------------------------------------------------------------------------
	|
	| Here you may specify the slug of the default page for your application.
	|
	*/

	'default_page' => 'welcome',

	/*
	|--------------------------------------------------------------------------
	| Default Section area
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default section when using database storage
	| type.
	|
	*/

	'default_section' => 'content',

	/*
	|--------------------------------------------------------------------------
	| Default Template
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default template used for database pages.
	|
	*/

	'default_template' => 'layouts/default',

	/*
	|--------------------------------------------------------------------------
	| Default 404 Error Page
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default 404 page used for 404 error pages.
	|
	*/

	'not_found' => null,

	/*
	|--------------------------------------------------------------------------
	| Exclude directories
	|--------------------------------------------------------------------------
	|
	| Here you may specify the directories that you want to exclude from
	| the template listing.
	|
	*/

	'exclude' => array(

		'modals',
		'pages',
		'partials',

	),

);
