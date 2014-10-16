<?php namespace Platform\Pages\Transformers;
/**
 * Part of the Platform Pages extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Pages extension
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Pages\Models\Page;
use League\Fractal\TransformerAbstract;
use Platform\Pages\Repositories\PageRepositoryInterface;

class PageTransformer extends TransformerAbstract {

	/**
	 * {@inheritDoc}
	 */
	protected $availableIncludes = [
		'body',
	];

	/**
	 * The Page repository.
	 *
	 * @var \Platform\Pages\Repositories\PageRepositoryInterface
	 */
	protected $pages;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Pages\Repositories\PageRepositoryInterface  $pages
	 * @return void
	 */
	public function __construct(PageRepositoryInterface $pages)
	{
		$this->pages = $pages;
	}

	/**
	 * Returns the transformed data.
	 *
	 * @param  \Platform\Pages\Models\Page  $page
	 * @return array
	 */
	public function transform(Page $page)
	{
		return [
			'id'         => (int) $page->id,
			'name'       => $page->name,
			'slug'       => $page->slug,
			'created_at' => (string) $page->created_at,
		];
	}

	/**
	 * Include the Page body.
	 *
	 * @param  \Platform\Pages\Models\Page  $page
	 * @return \League\Fractal\ItemResource
	 */
	public function includeBody(Page $page)
	{
		return $this->item($page, new BodyTransformer($this->pages));
	}

}
