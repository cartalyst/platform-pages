<?php namespace Platform\Pages;
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

class Page extends \Illuminate\Database\Eloquent\Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	public $table = 'pages';

	/**
	 * Returns an array of the page groups.
	 *
	 * @return array
	 */
	public function groups()
	{
		$groups = array();

		foreach (json_decode($this->groups) as $group)
		{
			$groups[ $group ] = $group;
		}

		return $groups;
	}

	/**
	 * Create a new instance of the given model.
	 *
	 * @param  array  $attributes
	 * @param  bool   $exists
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function newInstance($attributes = array(), $exists = false)
	{
		// This method just provides a convenient way for us to
		// generate fresh model instances of this current model.
		// It is particularly useful during the hydration of new
		// objects via the Eloquent query builder instances.
		$model = new static((array) $attributes);

		$model->exists = $exists;

		return $model;
	}

}
