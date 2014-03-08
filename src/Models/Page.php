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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Themes\ThemeBag;
use Closure;
use Config;
use InvalidArgumentException;
use Platform\Attributes\Models\Entity;
use RuntimeException;
use Sentry;
use Str;
use Symfony\Component\Finder\Finder;
use View;

class Page extends Entity {

	/**
	 * {@inheritDoc}
	 */
	protected $table = 'pages';

	/**
	 * {@inheritDoc}
	 */
	protected $guarded = [
		'id',
		'menu',
		'parent',
		'created_at',
		'updated_at',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $with = [
		'values.attribute',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $eavNamespace = 'platform/pages';

	/**
	 * The theme bag which is used for rendering file-based pages.
	 *
	 * @var \Illuminate\View\Environment
	 */
	protected static $themeBag;

	/**
	 * The theme in which we render pages.
	 *
	 * @var string
	 */
	protected static $theme = null;

	/**
	 * The group model.
	 *
	 * @var string
	 */
	protected static $groupModel = 'Platform\Users\Group';

	/**
	 * The content model.
	 *
	 * @var string
	 */
	protected static $contentModel = 'Platform\Content\Models\Content';

	/**
	 * The menu model.
	 *
	 * @var string
	 */
	protected static $menuModel = 'Platform\Menus\Menu';

	/**
	 * {@inheritDoc}
	 */
	public static function find($id, $columns = ['*'])
	{
		$instance = new static;

		if ($page = $instance->newQuery()->whereSlug($id)->first($columns))
		{
			return $page;
		}

		return parent::find($id, $columns);
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(array $options = [])
	{
		parent::save($options);

		if ( ! empty($options))
		{
			$menuModel = with(new static::$menuModel);

			// Find the menu
			if ($menu = array_get($options, 'menu'))
			{
				if ($menu = $menuModel->find($menu))
				{
					// See if we have a menu for this page
					$pageMenu = $menuModel->where('page_id', $this->id)->first();

					// Get the parent id, if applicable
					$parentId = array_get($options, "parent.{$options['menu']}");

					if (is_null($pageMenu))
					{
						// Menu attributes
						$attrs = [
							'slug'    => $this->slug,
							'name'    => $this->name,
							'uri'     => $this->uri,
							'type'    => 'page',
							'page_id' => $this->id,
							'enabled' => 1,
						];
					}
					else
					{
						$guardedAttributes = $pageMenu->getGuarded();
						array_push($guardedAttributes, 'id');

						// Store menu attributes
						$attrs = array_except($pageMenu->getAttributes(), $guardedAttributes);

						$pageMenu->delete();
					}

					$pageMenu = new static::$menuModel($attrs);

					$destination = $parentId == 0 ? $menu : $menuModel->find($parentId);

					$pageMenu->makeLastChildOf($destination);
				}
			}
		}

		return true;
	}

	/**
	 * Get mutator for the "groups" attribute.
	 *
	 * @param  array  $groups
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function getGroupsAttribute($groups)
	{
		if ( ! $groups)
		{
			return [];
		}

		if (is_array($groups))
		{
			return $groups;
		}

		if ( ! $_groups = json_decode($groups, true))
		{
			throw new InvalidArgumentException("Cannot JSON decode groups [{$groups}].");
		}

		return $_groups;
	}

	/**
	 * Set mutator for the "groups" attribute.
	 *
	 * @param  array  $groups
	 * @return void
	 */
	public function setGroupsAttribute($groups)
	{
		$this->attributes['groups'] = ! empty($groups) ? json_encode($groups) : '';
	}

	/**
	 * Get mutator for the "enabled" attribute.
	 *
	 * @param  string  $enabled
	 * @return bool
	 */
	public function getEnabledAttribute($enabled)
	{
		return (bool) $enabled;
	}

	/**
	 * Set mutator the "slug" attribute.
	 *
	 * @param  string  $slug
	 * @return void
	 */
	public function setSlugAttribute($slug)
	{
		$this->attributes['slug'] = Str::slug(str_replace('_', '-', $slug ?: $this->name));
	}

	/**
	 * Set mutator for the "uri" attribute.
	 *
	 * @param  string  $uri
	 * @return void
	 */
	public function setUriAttribute($uri)
	{
		$this->attributes['uri'] = trim($uri);
	}

	/**
	 * Set mutator for the "template" attribute.
	 *
	 * @param  string  $template
	 * @return void
	 */
	public function setTemplateAttribute($template)
	{
		$this->attributes['template'] = ($this->type === 'filesystem' ? null : $template);
	}

	/**
	 * Set mutator for the "section" attribute.
	 *
	 * @param  string  $section
	 * @return void
	 */
	public function setSectionAttribute($section)
	{
		$this->attributes['section'] = ($this->type === 'filesystem' ? null : $section);
	}

	/**
	 * Set mutator for the "value" attribute.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setValueAttribute($value)
	{
		$this->attributes['value'] = ($this->type === 'filesystem' ? null : $value);
	}

	/**
	 * Set mutator for the "file" attribute.
	 *
	 * @param  string  $file
	 * @return void
	 */
	public function setFileAttribute($file)
	{
		$this->attributes['file'] = ($this->type === 'database' ? null : $file);
	}

	/**
	 * Renders the page.
	 *
	 * @return string
	 * @throws \RuntimeException
	 */
	public function render()
	{
		$page = $this;

		$type = $this->type;

		if (in_array($type, ['filesystem', 'database']))
		{
			$view = "pages/{$this->file}";

			if ($type === 'database')
			{
				// Get the content model
				$contentModel = app($this->getContentModel());
				$value = $contentModel->prepareContent($this->value);

				// We'll inject the section with the value, i.e. @content()
				$result = static::$themeBag->getViewEnvironment()->inject($this->section, $value);

				$view = $this->template;
			}

			$data = array_merge($this->additionalRenderData(), compact('page'));

			return static::$themeBag->view($view, $data, static::$theme)->render();
		}

		throw new RuntimeException("Invalid storage type [{$type}] for page [{$this->getKey()}].");
	}

	/**
	 * Grabs additional rendering data by firing a callback which
	 * people can listen into.
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	protected function additionalRenderData()
	{
		$page = $this;

		$responses = static::$dispatcher->fire("platform/pages::rendering.{$page->slug}", compact('page'));

		$data = [];

		foreach ($responses as $response)
		{
			// If nothing was returned, the page was probably
			// modified or something else occured.
			if (is_null($response)) continue;

			if ( ! is_array($response))
			{
				throw new InvalidArgumentException('Page rendering event listeners must return an array or must not return anything at all.');
			}

			foreach ($response as $key => $value)
			{
				$data[$key] = $value;
			}
		}

		if (array_key_exists('page', $data))
		{
			throw new RuntimeException('Cannot set [page] additional data for a page as it is reserved.');
		}

		return $data;
	}

	/**
	 * Get the theme bag instance.
	 *
	 * @return \Cartalyst\Themes\ThemeBag
	 */
	public static function getThemeBag()
	{
		return static::$themeBag;
	}

	/**
	 * Set the theme bag instance.
	 *
	 * @param  \Cartalyst\Themes\ThemeBag  $themeBag
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
	 * @param  string  $model
	 * @return void
	 */
	public static function setGroupModel($model)
	{
		static::$groupModel = $model;
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
	 * Get the content model.
	 *
	 * @return string
	 */
	public static function getContentModel()
	{
		return static::$contentModel;
	}

	/**
	 * Set the content model.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public static function setContentModel($model)
	{
		static::$contentModel = $model;
	}

	/**
	 * Unset the content model.
	 *
	 * @return void
	 */
	public static function unsetContentModel()
	{
		static::$contentModel = null;
	}

	/**
	 * Get the menu model.
	 *
	 * @return string
	 */
	public static function getMenuModel()
	{
		return static::$menuModel;
	}

	/**
	 * Set the menu model.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public static function setMenuModel($model)
	{
		static::$menuModel = $model;
	}

	/**
	 * Unset the menu model.
	 *
	 * @return void
	 */
	public static function unsetMenuModel()
	{
		static::$groupModel = null;
	}

	/**
	 * Add a callback for when a page is rendering.
	 *
	 * @param  \Closure  $callback
	 * @return void
	 */
	public static function rendering(Closure $callback)
	{
		static::$dispatcher->listen('platform/pages::rendering.*', $callback);
	}

}
