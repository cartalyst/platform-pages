<?php namespace Platform\Pages\Repositories;
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

use RuntimeException;
use Cartalyst\Support\Traits;
use Cartalyst\Themes\ThemeBag;
use Platform\Pages\Models\Page;
use Illuminate\Container\Container;
use Symfony\Component\Finder\Finder;

class PageRepository implements PageRepositoryInterface {

	use Traits\EventTrait, Traits\RepositoryTrait, Traits\ValidatorTrait;

	/**
	 * The container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * The Eloquent page model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * The theme bag which is used for rendering file-based pages.
	 *
	 * @var \Illuminate\View\Factory
	 */
	protected $themeBag;

	/**
	 * The theme in which we render pages.
	 *
	 * @var string
	 */
	protected $theme = null;

	/**
	 * The menu model.
	 *
	 * @var string
	 */
	protected $menuModel = 'Platform\Menus\Models\Menu';

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;

		$this->setDispatcher($this->app['events']);

		$this->setValidator($app['platform.content.validator']);

		$this->model = get_class($this->app['Platform\Pages\Models\Page']);
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
		return $this->createModel()->rememberForever('platform.pages.all')->get();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAllEnabled()
	{
		return $this->createModel()->rememberForever('platform.pages.all.enabled')->whereEnabled(1)->get();
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		$model = $this->createModel()->rememberForever('platform.page.'.$id);

		if (is_numeric($id))
		{
			return $model->find($id);
		}

		return $model->orWhere('slug', $id)->orWhere('uri', $id)->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findEnabled($id)
	{
		return $this
			->createModel()
			->rememberForever('platform.page.enabled'.$id)
			->where('enabled', 1)
			->whereNested(function($query) use ($id)
			{
				$query
					->orWhere('slug', $id)
					->orWhere('uri', $id)
					->orWhere('id', (int) $id);
			})
			->first();
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
		if ( ! $menu = $this->app['platform.menus']->findWhere('page_id', (int) $page->id))
		{
			$menu = $this->app['platform.menus']->createModel();
		}

		// Get all the available page files
		$files = $this->files();

		// Get all the available templates
		$templates = $this->templates();

		// Get all the available user roles
		$roles = $this->app['platform.roles']->findAll();

		// Get the all the menu root items
		$menus = $this->app['platform.menus']->findAllRoot();

		return compact('page', 'roles', 'templates', 'menus');
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $data)
	{
		return $this->validator->validate($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
		$page = $this->find($id);

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
		$data = $this->fireEvent('platform.page.creating', [ $input ])[0];

		// Validate the submitted data
		$messages = $this->validForCreation($data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Save the page
			$page->fill($data)->save();

			//
			$this->setPageMenu($page, $data);

			// Fire the 'platform.page.created' event
			$this->fireEvent('platform.page.created', $page);
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
		$data = $this->fireEvent('platform.page.updating', [ $page, $input ])[0];

		// Validate the submitted data
		$messages = $this->validForUpdate($id, $data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Update the page
			$page->fill($data)->save();

			//
			$this->setPageMenu($page, $data);

			// Fire the 'platform.page.updated' event
			$this->fireEvent('platform.page.updated', $page);
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
			$this->fireEvent('platform.page.deleted', $page);

			// Delete the content
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
		return $this->update($id, ['enabled' => 1]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function disable($id)
	{
		return $this->update($id, ['enabled' => 0]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function render(Page $page)
	{
		$type = $page->type;

		if (in_array($type, ['filesystem', 'database']))
		{
			$view = "pages/{$page->file}";

			if ($type === 'database')
			{
				// Get the content repository
				$repository = app('Platform\Content\Repositories\ContentRepositoryInterface');

				$value = $repository->prepareForRendering(0, $page->value);

				// We'll inject the section with the value, i.e. @content()
				$result = $this->getThemeBag()->getViewFactory()->inject($page->section, $value);

				$view = $page->template;
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
		$extensions = array_keys(view()->getExtensions());

		$paths = array_filter(array_map(function($path) {

			// Full path to the pages folder
			$fullPath = implode(DIRECTORY_SEPARATOR, [$path, 'pages']);

			// Check if the path exists
			$pathConfig = head(config('cartalyst/themes::paths'));

			$searchPath = str_replace($pathConfig, '', $path);

			if (is_dir($fullPath) && strpos($searchPath, 'admin') == false)
			{
				return $fullPath;
			}

		}, $this->getThemeBag()->getCascadedViewPaths($this->getTheme())));

		if (empty($paths)) return [];

		$finder = with(new Finder)->files()->in($paths);

		$files = [];

		// Replace all file extensions with nothing. pathinfo()
		// won't tackle ".blade.php" so this is our best shot.
		$replacements = array_pad([], count($extensions), '');

		foreach ($finder as $file)
		{
			$file = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());

			// Because we want to save a valid source for the view loader, we
			// simply want to store the view name as if the view loader was
			// loading it.
			$files[rtrim(str_replace($extensions, $replacements, $file), '.')] = $file;
		}

		return $files;
	}

	/**
	 * {@inheritDoc}
	 */
	public function templates()
	{
		$extensions = array_keys(view()->getExtensions());

		$paths = array_filter(array_map(function($path)
		{
			$pathConfig = head(config('cartalyst/themes::paths'));

			$searchPath = str_replace($pathConfig, '', $path);

			if (strpos($searchPath, 'admin') == false)
			{
				return $path;
			}
		}, $this->getThemeBag()->getCascadedViewPaths($this->getTheme())));

		if (empty($paths)) return [];

		$finder = new Finder;
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
			$files[rtrim(str_replace($extensions, $replacements, $file), '.')] = $file;
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
			$this->themeBag = $this->app['themes'];
		}

		return $this->themeBag;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTheme()
	{
		if ( ! $this->theme)
		{
			$this->theme = $this->app['config']->get('cartalyst/themes::active');
		}

		return $this->theme;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMenuModel()
	{
		return $this->menuModel;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMenuModel($model)
	{
		$this->menuModel = $model;

		return $this;
	}


	/**
	 * {@inheritDoc}
	 */
	protected function setPageMenu($page, array $options = [])
	{
		if ( ! empty($options))
		{
			$menuModel = with(new $this->menuModel);

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
						$pageMenu = new $this->menuModel(array(
							'slug'    => $page->slug,
							'name'    => $page->name,
							'uri'     => $page->uri,
							'type'    => 'page',
							'page_id' => $page->id,
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

		$responses = $dispatcher->fire("platform/pages::rendering.{$page->slug}", compact('page'));

		$data = [];

		foreach ($responses as $response)
		{
			// If nothing was returned, the page was probably
			// modified or something else occured.
			if (is_null($response)) continue;

			if ( ! is_array($response))
			{
				throw new InvalidArgumentException('Page rendering event listeners must return an array or must not return anything at all.');
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
