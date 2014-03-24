<?php namespace Platform\Pages\Transformers;
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

use League\Fractal\TransformerAbstract;
use Platform\Pages\Models\Page;

class PageTransformer extends TransformerAbstract {

	/**
	 * {@inheritDoc}
	 */
	public function transform(Page $page)
	{
		return [
			'id'         => (int) $page->id,
			'name'       => $page->name,
			'slug'       => $page->slug,
			'body'       => $page->render(),
			'created_at' => (string) $page->created_at,
		];
	}

}
