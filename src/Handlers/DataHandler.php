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

namespace Platform\Pages\Handlers;

use Illuminate\Support\Arr;

class DataHandler implements DataHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepare(array $data)
    {
        $data['roles'] = Arr::get($data, 'roles', []);

        if (isset($data['uri']) && $data['uri'] !== '/') {
            $data['uri'] = trim($data['uri'], '/');
        }

        return $data;
    }
}
