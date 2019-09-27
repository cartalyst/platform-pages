<?php

/*
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
 * @version    8.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Pages\Menus;

use Illuminate\Support\Arr;
use Platform\Menus\Models\Menu;
use Platform\Menus\Types\AbstractType;
use Platform\Menus\Types\TypeInterface;

class PageType extends AbstractType implements TypeInterface
{
    /**
     * Holds all the available pages.
     *
     * @var \Platform\Pages\Models\Page
     */
    protected $pages;

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'page';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Page';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormHtml(Menu $child = null)
    {
        $pages = $this->getPages();

        return $this->view->make('platform/pages::types/form', compact('child', 'pages'));
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateHtml()
    {
        $pages = $this->getPages();

        return $this->view->make('platform/pages::types/template', compact('pages'));
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave(Menu $child)
    {
        $data = $child->getAttributes();

        if ($pageId = Arr::get($data, 'page_id')) {
            $page = $this->app['platform.pages']->find($pageId);

            $child->page_id = $pageId;

            $child->uri = $page->uri;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete(Menu $child)
    {
    }

    /**
     * Return all the available pages.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getPages()
    {
        if (! is_null($this->pages)) {
            return $this->pages;
        }

        $pages = $this->app['platform.pages']->findAll();

        foreach ($pages as $page) {
            $page->uri = $page->uri === '/' ? null : $page->uri;
        }

        return $this->pages = $pages;
    }
}
