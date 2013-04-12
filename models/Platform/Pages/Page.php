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
		// Do we have groups?
		if (is_null($this->groups))
		{
			return array();
		}

		$groups = array();

		foreach (json_decode($this->groups) as $group)
		{
			$groups[ $group ] = $group;
		}

		return $groups;
	}

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array(
		'name',
		'slug',
		'status',
		'type',
		'template',
		'visibility',
		'value',
	);

}
