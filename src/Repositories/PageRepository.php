<?php namespace Platform\Pages\Repositories;
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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use RuntimeException;
use InvalidArgumentException;
use Cartalyst\Support\Traits;
use Cartalyst\Themes\ThemeBag;
use Platform\Pages\Models\Page;
use Illuminate\Container\Container;
use Symfony\Component\Finder\Finder;

class PageRepository implements PageRepositoryInterface {

	use Traits\ContainerTrait, Traits\EventTrait, Traits\RepositoryTrait, Traits\ValidatorTrait;

	/**
	 * The Data handler.
	 *
	 * @var \Platform\Pages\Handlers\DataHandlerInterface
	 */
	protected $data;

	/**
	 * The Eloquent page model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * The theme bag which is used for rendering file-based pages.
	 *
	 * @var \Cartalyst\Themes\ThemeBag
	 */
	protected $themeBag;

	/**
	 * The themes to render pages from.
	 *
	 * @var array
	 */
	protected $theme = [];

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
		$this->setContainer($app);

		$this->setDispatcher($app['events']);

		$this->data = $app['platform.pages.handler.data'];

		$this->setValidator($app['platform.pages.validator']);

		$this->setModel(get_class($app['Platform\Pages\Models\Page']));
	}

	/**
	 * {@inheritDoc}
	 */
	public function grid()
	{
		return $this->createModel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll()
	{
		return $this->container['cache']->rememberForever('platform.pages.all', function()
		{
			return $this->createModel()->get();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAllEnabled()
	{
		return $this->container['cache']->rememberForever('platform.pages.all.enabled', function()
		{
			return $this->createModel()->whereEnabled(1)->get();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		if ($id instanceof Page) return $id;

		if (is_numeric($id))
		{
			return $this->container['cache']->rememberForever('platform.page.'.$id, function() use ($id)
			{
				return $this->createModel()->find($id);
			});
		}

		return $this->findBySlug($id) ?: $this->findByUri($id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findBySlug($slug)
	{
		return $this->container['cache']->rememberForever('platform.page.slug.'.$slug, function() use ($slug)
		{
			return $this->createModel()->whereSlug($slug)->first();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByUri($uri)
	{
		return $this->container['cache']->rememberForever('platform.page.uri.'.$uri, function() use ($uri)
		{
			return $this->createModel()->whereUri($uri)->first();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function findEnabled($id)
	{
		return $this->container['cache']->rememberForever('platform.page.enabled.'.$id, function() use ($id)
		{
			return $this
				->createModel()
				->where('enabled', 1)
				->whereNested(function($query) use ($id)
				{
					$query
						->orWhere('slug', $id)
						->orWhere('uri', $id)
						->orWhere('id', (int) $id);
				})
				->first();
		});
	}
	/**
	 * {@inheritDoc}
	 */
	public function getPreparedPage($id)
	{
		if ( ! is_null($id))
		{
			if ( ! $page = $this->find($id)) return false;
		}
		else
		{
			$page = $this->createModel();
		}

		// Find this page menu
		if ( ! $menu = $this->container['platform.menus']->findWhere('page_id', (int) $page->id))
		{
			$menu = $this->container['platform.menus']->createModel();
		}

		// Get all the available page files
		$files = $this->files();

		// Get all the available templates
		$templates = $this->templates();

		// Get all the available user roles
		$roles = $this->container['platform.roles']->findAll();

		// Get all the root menu items
		$menus = $this->container['platform.menus']->findAllRoot();

		return compact('page', 'files', 'roles', 'templates', 'menus', 'menu');
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $data)
	{
		return $this->validator->on('create')->validate($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate(Page $page, array $data)
	{
		$bindings = [ 'slug' => $page->slug, 'uri' => $page->uri ];

		return $this->validator->on('update')->bind($bindings)->validate($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $input)
	{
		// Create a new page
		$page = $this->createModel();

		// Fire the 'platform.page.creating' event
		if ($this->fireEvent('platform.page.creating', [ $input ]) === false)
		{
			return false;
		}

		// Prepare the submitted data
		$data = $this->data->prepare($input);

		// Validate the submitted data
		$messages = $this->validForCreation($data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Save the page
			$page->fill($data)->save();

			// Create the page menu
			$this->setPageMenu($page, $data);

			// Fire the 'platform.page.created' event
			$this->fireEvent('platform.page.created', [ $page ]);
		}

		return [ $messages, $page ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $input)
	{
		// Get the page object
		$page = $this->find($id);

		// Fire the 'platform.page.updating' event
		if ($this->fireEvent('platform.page.updating', [ $page, $input ]) === false)
		{
			return false;
		}

		// Prepare the submitted data
		$data = $this->data->prepare($input);

		// Validate the submitted data
		$messages = $this->validForUpdate($page, $data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Update the page
			$page->fill($data)->save();

			// Update the page menu
			$this->setPageMenu($page, $data);

			// Fire the 'platform.page.updated' event
			$this->fireEvent('platform.page.updated', [ $page ]);
		}

		return [ $messages, $page ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function store($id, array $input)
	{
		return ! $id ? $this->create($input) : $this->update($id, $input);
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		// Check if the page exists
		if ($page = $this->find($id))
		{
			// Fire the 'platform.page.deleted' event
			$this->fireEvent('platform.page.deleted', [ $page ]);

			// Delete the page
			$page->delete();

			return true;
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function enable($id)
	{
		$this->validator->bypass();

		return $this->update($id, [ 'enabled' => true ]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function disable($id)
	{
		$this->validator->bypass();

		return $this->update($id, [ 'enabled' => false ]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function render(Page $page)
	{
		$type = $page->type;

		if (in_array($type, ['filesystem', 'database']))
		{
			$view = $type === 'database' ? $page->template : "pages/{$page->file}";

			if ($type === 'database')
			{
				$value = app('platform.content')->prepareForRendering(0, $page->value);

				// We'll inject the section with the value
				$this->getThemeBag()->getViewFactory()->inject($page->section, $value);
			}

			$data = array_merge($this->additionalRenderData($page), compact('page'));

			return $this->getThemeBag()->view($view, $data, $this->getTheme())->render();
		}

		throw new RuntimeException("Invalid storage type [{$type}] for page [{$page->getKey()}].");
	}

	/**
	 * {@inheritDoc}
	 */
	public function files()
	{
		$paths = $this->getFilePaths();

		if (empty($paths)) return [];

		$files = [];

		// Get all the file extensions registered with Blade
		$extensions = array_keys(view()->getExtensions());
		$extensions = array_map(function($extension)
		{
			return '.'.$extension;
		}, $extensions);

		// Replace all file extensions with nothing. pathinfo()
		// won't tackle ".blade.php" so this is our best shot.
		$replacements = array_pad([], count($extensions), '');

		// Loop through the paths
		foreach ($this->getFinder()->files()->in($paths) as $file)
		{
			$file = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());

			// Because we want to save a valid source for the view loader, we simply
			// want to store the view name as if the view loader was loading it.
			$files[str_replace($extensions, $replacements, $file)] = $file;
		}

		return $files;
	}

	/**
	 * {@inheritDoc}
	 */
	public function templates()
	{
		$extensions = array_keys(view()->getExtensions());

		$paths = $this->getTemplatePaths();

		if (empty($paths)) return [];

		$finder = $this->getFinder();
		$finder->in($paths);
		$finder->depth('< 3');
		$finder->exclude(config('platform/pages::exclude'));
		$finder->name(sprintf(
			'/.*?\.(%s)/',
			implode('|', array_map(function($extension)
			{
				return preg_quote($extension, '/');
			}, $extensions))
		));

		$extensions = array_map(function($extension)
		{
			return '.'.$extension;
		}, $extensions);

		$files = [];

		// Replace all file extensions with nothing. pathinfo()
		// won't tackle ".blade.php" so this is our best shot.
		$replacements = array_pad([], count($extensions), '');

		foreach ($finder->files() as $file)
		{
			$file = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());

			// Because we want to save a valid source for the view loader, we
			// simply want to store the view name as if the view loader was
			// loading it.
			$files[str_replace($extensions, $replacements, $file)] = $file;
		}

		return $files;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getThemeBag()
	{
		if ( ! $this->themeBag)
		{
			$this->themeBag = $this->container['themes'];
		}

		return $this->themeBag;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTheme($type = 'active')
	{
		if ( ! isset($this->theme[$type]))
		{
			$this->theme[$type] = $this->container['config']->get("platform/themes::{$type}.frontend");
		}

		return $this->theme[$type];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFinder()
	{
		return new Finder();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTemplatePaths()
	{
		$themePaths = $this->getThemePaths();

		return array_filter(array_map(function($path)
		{
			$pathConfig = head(config('cartalyst/themes::paths'));

			$searchPath = str_replace($pathConfig, '', $path);

			if (strpos($searchPath, 'admin') == false)
			{
				return $path;
			}
		}, $themePaths));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFilePaths()
	{
		$themePaths = $this->getThemePaths();

		return array_filter(array_map(function($path)
		{
			$pathConfig = head(config('cartalyst/themes::paths'));

			// Full path to the content folder
			$fullPath = implode(DIRECTORY_SEPARATOR, [$path, 'pages']);

			// Check if the path exists
			$searchPath = str_replace($pathConfig, '', $path);

			if ($this->container['files']->isDirectory($fullPath) && strpos($searchPath, 'admin') == false)
			{
				return $fullPath;
			}
		}, $themePaths));
	}

	/**
	 * Returns the theme paths.
	 *
	 * @return array
	 */
	protected function getThemePaths()
	{
		$themePaths = [];

		foreach ([$this->getTheme(), $this->getTheme('fallback')] as $theme)
		{
			$themePaths[] = $this->getThemeBag()->getCascadedViewPaths($theme);
		}

		return array_flatten($themePaths);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function setPageMenu($page, array $options = [])
	{
		if ( ! empty($options))
		{
			$menuModel = $this->container['platform.menus']->createModel();

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
					if ( ! $pageMenu = $menuModel->where('page_id', $page->id)->first())
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
						$pageMenu = $this->container['platform.menus']->createModel([
							'slug'    => $page->slug,
							'name'    => $page->name,
							'uri'     => $page->uri,
							'type'    => 'page',
							'page_id' => $page->id,
							'enabled' => 1,
						]);

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
	}

	/**
	 * Grabs additional rendering data by firing a callback which
	 * people can listen into.
	 *
	 * @param  \Platform\Pages\Models\Page  $page
	 * @return array
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	protected function additionalRenderData(Page $page)
	{
		$dispatcher = $page->getEventDispatcher();

		$responses = $dispatcher->fire('platform.pages.rendering.'.$page->slug, compact('page'));

		$data = [];

		foreach ($responses as $response)
		{
			// If nothing was returned, the page was probably
			// modified or something else occured.
			if (is_null($response)) continue;

			if ( ! is_array($response))
			{
				throw new InvalidArgumentException('Page rendering event listeners must return an array or null.');
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

}
