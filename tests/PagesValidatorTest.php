<?php

/*
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
 * @version    8.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Pages\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Platform\Pages\Validator\PagesValidator;

class PagesValidatorTest extends TestCase
{
    /**
     * Validator instance.
     *
     * @var \Platform\Pages\Validator\PagesValidator
     */
    protected $validator;

    /**
     * Close mockery.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new PagesValidator(m::mock('Illuminate\Validation\Factory'));
    }

    /** @test */
    public function it_can_validate()
    {
        $rules = [
            'name'       => 'required|max:255',
            'slug'       => 'required|max:255|unique:pages',
            'uri'        => 'required|max:255|unique:pages',
            'enabled'    => 'required',
            'type'       => 'required|in:database,filesystem',
            'visibility' => 'required|in:always,logged_in,admin',
            'template'   => 'required_if:type,database',
            'file'       => 'required_if:type,filesystem',
        ];

        $this->assertSame($rules, $this->validator->getRules());

        $this->validator->onUpdate();

        $rules['slug'] .= ',slug,{slug},slug';
        $rules['uri']  .= ',uri,{uri},uri';

        $this->assertSame($rules, $this->validator->getRules());
    }
}
