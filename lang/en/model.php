<?php

/**
 * Part of the Platform Pages extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Pages extension
 * @version    3.2.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return [

    'general' => [

        'legend'        => 'Details',

        'name'          => 'Name',
        'name_help'     => 'Type a descriptive name for your page.',

        'slug'          => 'Slug',
        'slug_help'     => 'Single word, no spaces, no special words. Dashes are allowed.',

        'uri'           => 'Uri',
        'uri_help'      => 'Your page uri.',

        'https'         => 'Https',
        'https_help'    => 'Should this page be presented over HTTPS?',

        'enabled'       => 'Status',
        'enabled_help'  => 'What is the status of this page?',

        'type'          => 'Storage Type',
        'type_help'     => 'How do you want to store and edit this page?',

        'database'      => 'Database',
        'filesystem'    => 'Filesystem',

        'template'      => 'Template',
        'template_help' => 'Page template to use.',

        'section'       => 'Section',
        'section_help'  => 'Which @section() to inject value to?',

        'value'         => 'Markup',
        'value_help'    => "The page's markup. @content call is allowed.",

        'file'          => 'File',
        'file_help'     => 'Choose the file to use when rendering this page.',

    ],

    'access' => [

        'legend'          => 'Access Control',

        'visibility'      => 'Visibility',
        'visibility_help' => 'Select when this page can be accessed.',

        'always'          => 'Show Always',
        'logged_in'       => 'Logged In',
        'logged_out'      => 'Logged Out',
        'admin'           => 'Admin Only',

        'roles'           => 'Roles',
        'roles_help'      => 'Restrict access to user roles.',

    ],

    'navigation' => [

        'legend'      => 'Navigation',

        'menu'        => 'Menu',
        'menu_help'   => 'Add this page to your navigation.',

        'select_menu' => '-- Select a Menu --',
        'top_level'   => '-- Top Level --',

    ],

];
