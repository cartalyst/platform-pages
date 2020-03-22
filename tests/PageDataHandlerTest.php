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
 * @version    9.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Pages\Tests;

use PHPUnit\Framework\TestCase;
use Platform\Pages\Handlers\DataHandler;

class PageDataHandlerTest extends TestCase
{
    /**
     * Model instance.
     *
     * @var \Platform\Pages\Handlers\DataHandler
     */
    protected $handler;

    /**
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->handler = new DataHandler();
    }

    /** @test */
    public function it_can_prepare_data()
    {
        $data = [];

        $expected = [
            'roles' => [],
        ];

        $this->assertSame($expected, $this->handler->prepare($data));
    }
}
