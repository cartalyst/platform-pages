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

return array(

	'name'            => 'Name',
	'name_help'       => 'Type a descriptive name for your page.',
	'slug'            => 'Slug',
	'slug_help'       => 'Slug to find page by.',
	'status'          => 'Status',
	'enabled_help'    => 'What is the status of this page?',
	'type'            => 'Storage Type',
	'type_help'       => 'How do you want to store and edit this page?',
	'visibility'      => array(
		'legend' => 'Visibility',

		'always'     => 'Show Always',
		'logged_in'  => 'Logged In',
		'logged_out' => 'Logged Out',
		'admin'      => 'Admin Only',
	),
	'visibility_help' => 'When should this page be seen?',
	'groups'          => 'Groups',
	'groups_help'     => 'What user groups should be able to see this page?',
	'template'        => 'Template',
	'template_help'   => 'Page template to use.',
	'section'         => 'Section',
	'section_help'    => 'Which @section() to inject value to?',
	'value'           => 'Value',
	'value_help'      => "The page's value. @content call is allowed.",
	'file'            => 'File',
	'file_help'       => 'File to use.',

	'create' => array(
		'legend'  => 'Add Page',
		'summary' => 'Please supply the following information.',
	),

	'update' => array(
		'legend'  => 'Edit Page',
		'summary' => 'Please supply the following information.',
	),

	'copy' => array(
		'legend'  => 'Copy Page',
		'summary' => 'Please supply the following information.',
	),
);
