<?php namespace Platform\Pages\Tests;
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
 * @version    1.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use PHPUnit_Framework_TestCase;
use Platform\Pages\Handlers\DataHandler;

class PageDataHandlerTest extends PHPUnit_Framework_TestCase {

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
	public function setUp()
	{
		$this->handler = new DataHandler;
	}

	/** @test */
	public function it_can_prepare_data()
	{
		$data = [];

		$expected = [
			'roles' => [],
		];

		$this->assertEquals($expected, $this->handler->prepare($data));
	}

}
