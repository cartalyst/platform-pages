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

use Illuminate\View\Environment;

class Page extends \Illuminate\Database\Eloquent\Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'pages';

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

	/**
	 * The view environemnt which is
	 * used for rendering file-based pages.
	 *
	 * @param  Illuminate\View\Environment
	 */
	protected static $view;

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
	 * Returns the "value" attribute for the page.
	 *
	 * @return string
	 */
	public function getValueAttribute()
	{
		switch ($this->type)
		{
			case 'filesystem':
				return static::$view->make($this->template)->render();

			default:
				return $this->attributes['type'];
		}
	}

	/**
	 * Get the view environemtn instance.
	 *
	 * @return \Illuminate\View\Environment
	 */
	public static function getViewEnvironment()
	{
		return static::$view;
	}

	/**
	 * Set the view environemtn instance.
	 *
	 * @param  \Illuminate\View\Environment
	 * @return void
	 */
	public static function setViewEnvironment(Environment $view)
	{
		static::$view = $view;
	}

	/**
	 * Unset the view environemtn for models.
	 *
	 * @return void
	 */
	public static function unsetViewEnvironment()
	{
		static::$view = null;
	}

}
