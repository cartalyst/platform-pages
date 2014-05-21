<?php
/**
 * Part of the Platform Pages extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Pages extension
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return [

	'name'      => 'Name',
	'name_help' => 'Type a descriptive name for your page.',

	'slug'      => 'Short name',
	'slug_help' => 'Single word, no spaces, no special words. Dashes are allowed.',

	'uri'      => 'Uri',
	'uri_help' => 'Your page uri.',

	'ssl'         => 'Https (SSL)?',
	'ssl_enabled' => 'Select yes if you want this page to be presented over SSL.',

	'enabled'      => 'Status',
	'enabled_help' => 'What is the status of this page?',

	'type'      => 'Storage Type',
	'type_help' => 'How do you want to store and edit this page?',

	'visibility' => [
		'legend' => 'Visibility',

		'always'     => 'Show Always',
		'logged_in'  => 'Logged In',
		'logged_out' => 'Logged Out',
		'admin'      => 'Admin Only',
	],
	'visibility_help' => 'When should this page be seen?',

	'groups'      => 'Groups',
	'groups_help' => 'What user groups should be able to see this page?',

	'navigation' => [
		'legend' => 'Navigation',

		'menu'        => 'Menu',
		'select_menu' => '-- Select a Menu --',
		'top_level'   => '-- Top Level --',
	],
	'navigation_help' => 'Add this page to your navigation.',

	'template'      => 'Template',
	'template_help' => 'Page template to use.',

	'meta_title'      => 'Meta Title',
	'meta_title_help' => 'Meta Title tag.',

	'meta_description'      => 'Meta Description',
	'meta_description_help' => 'Meta Description tag.',

	'section'      => 'Section',
	'section_help' => 'Which @section() to inject value to?',

	'value'      => 'Value',
	'value_help' => "The page's value. @content call is allowed.",

	'file'      => 'File',
	'file_help' => 'Choose the file to use when rendering this page.',

];
