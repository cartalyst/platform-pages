<?php namespace Platform\Pages\Menus;
/**
 * Part of the Platform package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Menus\Models\Menu;
use Platform\Menus\Types\BaseType;
use Platform\Menus\Types\TypeInterface;

class PageType extends BaseType implements TypeInterface {

	/**
	 * Holds all the available pages.
	 *
	 * @var \Platform\Pages\Models\Page
	 */
	protected $pages = null;

	/**
	 * Get the type identifier.
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
		return 'page';
	}

	/**
	 * Get a human friendly name for the type.
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'Page';
	}

	/**
	 * Return the form HTML template for a edit child of this type as well
	 * as creating new children.
	 *
	 * @param  \Platform\Menus\Models\Menu  $child
	 * @return \View
	 */
	public function getFormHtml(Menu $child = null)
	{
		$pages = $this->getPages();

		return $this->view->make("platform/pages::types/form", compact('child', 'pages'));
	}

	/**
	 * Return the HTML template used when creating a menu child of this type.
	 *
	 * @return \View
	 */
	public function getTemplateHtml()
	{
		$pages = $this->getPages();

		return $this->view->make("platform/pages::types/template", compact('pages'));
	}

	/**
	 * Event that is called after a menu children is saved.
	 *
	 * @param  \Platform\Menus\Models\Menu  $child
	 * @return void
	 */
	public function afterSave(Menu $child)
	{
		$data = $child->getTypeData();

		if ($pageId = array_get($data, 'page_id'))
		{
			$repository = app('Platform\Pages\Repositories\PageRepositoryInterface');

			$page = $repository->find($pageId);

			$child->uri = $page->uri;

			$child->page_id = $pageId;
		}
	}

	/**
	 * Return all the available pages.
	 *
	 * @return \Platform\Pages\Models\Page
	 */
	protected function getPages()
	{
		if ( ! is_null($this->pages))
		{
			return $this->pages;
		}

		$repository = app('Platform\Pages\Repositories\PageRepositoryInterface');

		$pages = $repository->findAll();

		foreach ($pages as &$page)
		{
			$page->uri = $page->uri === '/' ? null : $page->uri;
		}

		return $this->pages = $pages;
	}

}
