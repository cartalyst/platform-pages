<?php namespace Platform\Pages\Handlers;
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
 * @version    1.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class DataHandler implements DataHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function prepare(array $data)
	{
		$data['roles'] = array_get($data, 'roles', []);

		return $data;
	}

}
