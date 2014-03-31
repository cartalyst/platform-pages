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

use Closure;
use Config;
use InvalidArgumentException;
use Platform\Attributes\Models\Entity;
use Str;
use Symfony\Component\Finder\Finder;

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
	/*public function _save(array $options = [])
	{
		parent::save($options);

		if ( ! empty($options))
		{
			$menuModel = with(new static::$menuModel);

			// Get the menu that this page will be stored
			$pageMenuTree = (int) array_get($options, 'menu', null);

			// Get the menu parent id, if applicable
			$pageMenuParent = (int) array_get($options, "parent.{$pageMenuTree}");

			// Find the menu
			if ($pageMenuTree)
			{
				// Check if the menu tree exists
				if ($menuTree = $menuModel->whereMenu($pageMenuTree)->first())
				{
					$createMenu = false;

					// Check if we have a menu for this page
					if ( ! $pageMenu = $menuModel->where('page_id', $this->id)->first())
					{
						$createMenu = true;

						$destination = $pageMenuParent === 0 ? $menuTree : $menuModel->find($pageMenuParent);
					}
					else
					{
						// Are we changing from menu trees?
						if ((int) $pageMenu->menu !== $pageMenuTree)
						{
							$createMenu = true;

							$guardedAttributes = $pageMenu->getGuarded();
							array_push($guardedAttributes, 'id');

							// Store menu attributes
							$attrs = array_except($pageMenu->getAttributes(), $guardedAttributes);

							// Delete from the current menu tree
							$pageMenu->delete();

							$destination = $menuModel->whereMenu($pageMenuTree)->first();
						}

						// Make it a top level item
						else if ($pageMenuParent === 0 && (int) $pageMenu->getDepth() !== 1)
						{
							$pageMenu->makeLastChildOf($menuTree);
						}
						else if ($pageMenuParent !== 0 && $pageMenuParent != $pageMenu->id)
						{
							if ($menuParent = $menuModel->find($pageMenuParent))
							{
								if ($pageMenu->getParent()->id != $menuParent->id)
								{
									$destination = $menuParent;

									$pageMenu->makeLastChildOf($destination);
								}
							}
						}
					}

					// Are we creating the page menu?
					if ($createMenu)
					{
						$pageMenu = new static::$menuModel(array(
							'slug'    => $this->slug,
							'name'    => $this->name,
							'uri'     => $this->uri,
							'type'    => 'page',
							'page_id' => $this->id,
							'enabled' => 1,
						));

						$pageMenu->makeLastChildOf($destination);

						if (isset($attrs))
						{
							$pageMenu->fill($attrs)->save();
						}
					}
				}
			}
		}

		return true;
	}*/

	/**
	 * Get mutator for the "type" attribute.
	 *
	 * @param  string  $type
	 * @return string
	 */
	public function getTypeAttribute($type)
	{
		return ($this->exists || $type) ? $type : 'database';
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
		return ($this->exists || $enabled) ? (bool) $enabled : true;
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
	 * Get mutator for the "template" attribute.
	 *
	 * @param  string  $template
	 * @return string
	 */
	public function getTemplateAttribute($template)
	{
		return ($this->exists || $template) ? $template : Config::get('platform/pages::default_template');
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
	 * Get mutator for the "section" attribute.
	 *
	 * @param  string  $section
	 * @return string
	 */
	public function getSectionAttribute($section)
	{
		return ($this->exists || $section) ? $section : Config::get('platform/pages::default_section');
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
	 * Get mutator for the "visibility" attribute.
	 *
	 * @param  string  $visibility
	 * @return string
	 */
	public function getVisibilityAttribute($visibility)
	{
		return ($this->exists || $visibility) ? $visibility : 'always';
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
