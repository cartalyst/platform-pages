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

use Cartalyst\Themes\ThemeBag;

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
	 * The theme bag which is used for rendering file-based pages.
	 *
	 * @var Illuminate\View\Environment
	 */
	protected static $themeBag;

	/**
	 * The theme in which we render pages.
	 *
	 * @var string
	 */
	protected static $theme = '';

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
				return static::$themeBag->view($this->template, array(), static::$theme)->render();

			default:
				return $this->attributes['type'];
		}
	}

	/**
	 * Get the theme bag instance.
	 *
	 * @return Cartalyst\Themes\ThemeBag
	 */
	public static function getThemeBag()
	{
		return static::$themeBag;
	}

	/**
	 * Set the theme bag instance.
	 *
	 * @param  Cartalyst\Themes\ThemeBag  $themeBag
	 * @return void
	 */
	public static function setThemeBag(ThemeBag $themeBag)
	{
		static::$themeBag = $themeBag;
	}

	/**
	 * Unset the theme bag for models.
	 *
	 * @return void
	 */
	public static function unsetThemeBag()
	{
		static::$themeBag = null;
	}

	/**
	 * Get the theme name.
	 *
	 * @return string
	 */
	public static function getTheme()
	{
		return static::$theme;
	}

	/**
	 * Set the theme name.
	 *
	 * @param  string
	 * @return void
	 */
	public static function setTheme($theme)
	{
		static::$theme = $theme;
	}

	/**
	 * Unset the theme bag for models.
	 *
	 * @return void
	 */
	public static function unsetTheme()
	{
		static::$theme = null;
	}

}
