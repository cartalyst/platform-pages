<?php namespace Platform\Pages\Models;
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
use Illuminate\Database\Eloquent\Model;

class Page extends Model {

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
		'uri',
		'enabled',
		'type',
		'visibility',
		'template',
		'section',
		'value',
		'file',
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
	 * THe user model.
	 *
	 * @var string
	 */
	protected static $groupModel = 'Platform\Users\Models\Group';

	/**
	 * Get the groups for the page.
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function groups()
	{
		return $this->belongsToMany(static::$groupModel, 'pages_groups');
	}

	/**
	 * Renders the page.
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	public function render()
	{
		$title = $this->attributes['name'];
		switch ($type = $this->type)
		{
			case 'filesystem':
				return static::$themeBag->view($this->file, array(), static::$theme)->with('title', $title)->render();

			case 'database':

				// We'll inject the section with the value, i.e. @content()
				static::$themeBag->getViewEnvironment()->inject($this->section, $this->value);

				return static::$themeBag->view($this->template, array(), static::$theme)->with('title', $title)->render();
		}

		throw new \RuntimeException("Invalid storage type [$type] for page [{$this->getKey()}].");
	}

	/**
	 * Mutator for the "enabled" attribute.
	 *
	 * @param  string  $enabled
	 * @return bool
	 */
	public function getEnabledAttribute($enabled)
	{
		return (bool) $enabled;
	}

	/**
	 * Find a model by its primary key.
	 *
	 * @param  mixed  $id
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Model|Collection
	 */
	public static function find($id, $columns = array('*'))
	{
		$instance = new static;

		if ($page = $instance->newQuery()->where('slug', $id)->first($columns))
		{
			return $page;
		}

		return parent::find($id, $columns);
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
	 * @param  string  $theme
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

	/**
	 * Get the group model.
	 *
	 * @return string
	 */
	public static function getGroupModel()
	{
		return static::$groupModel;
	}

	/**
	 * Set the group model.
	 *
	 * @param  string
	 * @return void
	 */
	public static function setGroupModel($groupModel)
	{
		static::$groupModel = $groupModel;
	}

	/**
	 * Unset the group model for models.
	 *
	 * @return void
	 */
	public static function unsetGroupModel()
	{
		static::$groupModel = null;
	}

}
