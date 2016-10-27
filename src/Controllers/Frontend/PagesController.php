<?php

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
 * @version    4.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Pages\Controllers\Frontend;

use Illuminate\Routing\Router;
use Cartalyst\Sentinel\Sentinel;
use Platform\Foundation\Controllers\Controller;
use Platform\Pages\Repositories\PageRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PagesController extends Controller
{
    /**
     * The Pages repository.
     *
     * @var \Platform\Pages\Repositories\PageRepositoryInterface
     */
    protected $pages;

    /**
     * The Cartalyst Sentinel instance.
     *
     * @var \Cartalyst\Sentinel\Sentinel
     */
    protected $sentinel;

    /**
     * Constructor.
     *
     * @param  \Platform\Pages\Repositories\PageRepositoryInterface
     * @param  \Cartalyst\Sentinel\Sentinel
     * @param  \Illuminate\Routing\Router
     * @return void
     */
    public function __construct(PageRepositoryInterface $pages, Sentinel $sentinel)
    {
        parent::__construct();

        $this->sentinel = $sentinel;

        $this->pages = $pages;
    }

    /**
     * Render the page.
     *
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function page()
    {
        // Get the current uri
        $slug = static::$router->current()->getUri();

        // Make sure we have a page slug
        if ($slug === '/') {
            $slug = config('platform-pages.default_page');
        }

        // Find the requested page
        $page = $this->pages->findEnabled($slug);

        // Check if the page should only be accessed through https
        if ($page->https && ! request()->secure()) {
            return redirect()->secure(request()->getRequestUri());
        }

        // Check if the page has any visibility requirements
        if (in_array($page->visibility, ['logged_in', 'admin'])) {
            // At this stage the user isn't allowed to view the page
            $canView = false;

            // Get the logged in user
            if ($currentUser = $this->sentinel->getUser()) {
                // Now we'll assume that the user can view the
                // page, because he's definitely logged in.
                $canView = true;

                // If the user is not a 'superuser' we'll double check
                // his permissions to allow or deny page visibility.
                if (! $this->sentinel->hasAccess('superuser')) {
                    if ($page->visibility === 'admin' || ! empty($page->roles)) {
                        $canView = false;

                        foreach ($currentUser->roles as $role) {
                            if (in_array($role->id, $page->roles)) {
                                $canView = true;

                                break;
                            }
                        }
                    }
                }
            }

            if (! $canView) {
                throw new HttpException(403, "You don't have permission to access this page.");
            }
        }

        return $this->pages->render($page);
    }
}
