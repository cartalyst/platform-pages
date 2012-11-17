<?php

return array(

	'name'        => 'Pages',
	'slug'        => 'platform/pages',
	'author'      => 'Cartalyst LLC',
	'description' => 'An extension to manage pages and content.',
	'version'     => '2.0',
	'is_core'     => true,

	'autoload' => 'composer',

	'dependencies' => array(
		'platform/menus' => array(
			'composer' => 'platform/extension-menus',
		),
	),

	'routes' => function() {

		Route::any('test', function() {

			echo __FILE__;

		});
	}

);