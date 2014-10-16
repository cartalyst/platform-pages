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

use League\Fractal\TransformerAbstract;
use Platform\Pages\Models\Page;
use Platform\Pages\Repositories\PageRepositoryInterface;

class BodyTransformer extends TransformerAbstract {

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
		return [ $this->pages->render($page) ];
	}

}
