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
use Closure;
use Config;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Theme;
use View;

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
		'meta_title',
		'meta_description',
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
	protected static $theme = null;

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
		$page = $this;

		$type = $this->type;

		if (in_array($type, array('filesystem', 'database')))
		{
			$view = "pages/{$this->file}";

			if ($type === 'database')
			{
				// We'll inject the section with the value, i.e. @content()
				static::$themeBag->getViewEnvironment()->inject($this->section, $this->value);

				$view = $this->template;
			}

			$data = array_merge($this->additionalRenderData(), compact('page'));

			return static::$themeBag->view($view, $data, static::$theme)->render();
		}

		throw new RuntimeException("Invalid storage type [{$type}] for page [{$this->getKey()}].");
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
	 * Grabs additional rendering data by firing a callback which
	 * people can listen into.
	 *
	 * @return arary  $data
	 */
	protected function additionalRenderData()
	{
		$page = $this;

		$responses = static::$dispatcher->fire("platform/pages::rendering.{$page->slug}", compact('page'));
		$data      = array();

		foreach ($responses as $response)
		{
			// If nothing was was returned, the page was probably modified
			// or something else occured.
			if ($response === null) continue;

			if ( ! is_array($response))
			{
				throw new \InvalidArgumentException("Page rendering event listeners must return an array or must not return anything at all.");
			}

			foreach ($response as $key => $value)
			{
				$data[$key] = $value;
			}
		}

		if (array_key_exists('page', $data))
		{
			throw new \RuntimeException("Cannot set [page] additional data for a page as it is reserved.");
		}

		return $data;
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
	 * Unset the group model.
	 *
	 * @return void
	 */
	public static function unsetGroupModel()
	{
		static::$groupModel = null;
	}

	/**
	 * Returns a list of the available page files on the current active theme.
	 *
	 * @return array
	 */
	public static function getPageFiles()
	{
		$theme = Page::getTheme();

		$extensions = array_keys(View::getExtensions());

		$paths = array();

		// Loop through the view paths
		foreach (Theme::getCascadedViewPaths($theme) as $path)
		{
			// Full path to the pages folder
			$fullPath = implode(DIRECTORY_SEPARATOR, array($path, 'pages'));

			// Check if the path doesn't exist
			if ( ! is_dir($fullPath))
			{
				continue;
			}

			$paths[] = $fullPath;
		}

		$finder = with(new Finder)->files()->in($paths);

		$files = array();

		// Replace all file extensions with nothing. pathinfo()
		// won't tackle ".blade.php" so this is our best shot.
		$replacements = array_pad(array(), count($extensions), '');

		foreach ($finder as $file)
		{
			$file = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());

			// Because we want to save a valid source for the view loader, we
			// simply want to store the view name as if the view loader was
			// loading it.
			$files[rtrim(str_replace($extensions, $replacements, $file), '.')] = $file;
		}

		return $files;
	}

	/**
	 * Returns a list of the available templates of the current active theme.
	 *
	 * @return array
	 */
	public static function getTemplates()
	{
		$theme = Page::getTheme();

		$extensions = array_keys(View::getExtensions());

		$finder = new Finder;
		$finder->in(Theme::getCascadedViewPaths($theme));
		$finder->depth('< 3');
		$finder->exclude(Config::get('platform/pages::exclude'));
		$finder->name(sprintf(
			'/.*?\.(%s)/',
			implode('|', array_map(function($extension)
			{
				return preg_quote($extension, '/');
			}, $extensions))
		));

		$files = array();

		// Replace all file extensions with nothing. pathinfo()
		// won't tackle ".blade.php" so this is our best shot.
		$replacements = array_pad(array(), count($extensions), '');

		foreach ($finder->files() as $file)
		{
			$file = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());

			// Because we want to save a valid source for the view loader, we
			// simply want to store the view name as if the view loader was
			// loading it.
			$files[rtrim(str_replace($extensions, $replacements, $file), '.')] = $file;
		}

		return $files;
	}

	/**
	 * Add a callback for when a page is rendering.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public static function rendering(Closure $callback)
	{
		static::$dispatcher->listen('platform/pages::rendering.*', $callback);
	}

}
