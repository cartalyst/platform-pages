<?php namespace Platform\Pages\Menus;
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

use DB;
use Platform\Menus\BaseType;
use Platform\Menus\Models\Menu;
use Platform\Menus\TypeInterface;

class PageType extends BaseType implements TypeInterface {

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
		$pages = DB::table('pages')->get();

		return $this->view->make("platform/pages::types/form", compact('child', 'pages'));
	}

	/**
	 * Return the HTML template used when creating a menu child of this type.
	 *
	 * @return \View
	 */
	public function getTemplateHtml()
	{
		$pages = DB::table('pages')->get();

		return $this->view->make("platform/pages::types/template", compact('child', 'pages'));
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
			$page = DB::table('pages')->where('id', $pageId)->first();

			$child->uri = $page->uri;
			$child->page_id = $pageId;
		}
	}

}
