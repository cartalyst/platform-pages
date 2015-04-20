<?php namespace Platform\Pages\Models;
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
 * @version    1.0.5
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Closure;
use Illuminate\Support\Str;
use Cartalyst\Tags\TaggableTrait;
use Cartalyst\Tags\TaggableInterface;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Attributes\EntityInterface;
use Platform\Attributes\Traits\EntityTrait;
use Cartalyst\Support\Traits\NamespacedEntityTrait;

class Page extends Model implements EntityInterface, TaggableInterface {

	use EntityTrait, NamespacedEntityTrait, TaggableTrait;

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
	protected static $entityNamespace = 'platform/pages';

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
	 * Get mutator for the "roles" attribute.
	 *
	 * @param  string  $roles
	 * @return array
	 */
	public function getRolesAttribute($roles)
	{
		return json_decode($roles, true) ?: [];
	}

	/**
	 * Set mutator for the "roles" attribute.
	 *
	 * @param  array  $roles
	 * @return void
	 */
	public function setRolesAttribute(array $roles)
	{
		$this->attributes['roles'] = ! empty($roles) ? json_encode($roles) : '';
	}

	/**
	 * Get mutator for the "enabled" attribute.
	 *
	 * @param  int  $enabled
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
		return ($this->exists || $template) ? $template : config('platform/pages::default_template');
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
		return ($this->exists || $section) ? $section : config('platform/pages::default_section');
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
		static::$dispatcher->listen('platform.pages.rendering.*', $callback);
	}

}
