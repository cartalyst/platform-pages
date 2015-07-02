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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Pages\Validator;

use Cartalyst\Support\Validator;

class PagesValidator extends Validator implements PagesValidatorInterface
{
    /**
     * {@inheritDoc}
     */
    protected $rules = [
        'name'       => 'required|max:255',
        'slug'       => 'required|max:255|unique:pages',
        'uri'        => 'required|max:255|unique:pages',
        'enabled'    => 'required',
        'type'       => 'required|in:database,filesystem',
        'visibility' => 'required|in:always,logged_in,admin',
        'template'   => 'required_if:type,database',
        'file'       => 'required_if:type,filesystem',
    ];

    /**
     * {@inheritDoc}
     */
    public function onUpdate()
    {
        $this->rules['slug'] .= ',slug,{slug},slug';
        $this->rules['uri'] .= ',uri,{uri},uri';
    }
}
