<?php namespace Platform\Pages\Repositories;
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

use Cartalyst\Themes\ThemeBag;
use Config;
use Illuminate\Events\Dispatcher;
use Platform\Pages\Models\Page;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Validator;
use View;

class DbPageRepository implements PageRepositoryInterface {

	/**
	 * The Eloquent page model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $rules = [
		'name'       => 'required|max:255',
		'slug'       => 'required|max:255|unique:pages',
		'uri'        => 'required|max:255|unique:pages',
		'enabled'    => 'required',
		'type'       => 'required|in:database,filesystem',
		'visibility' => 'required|in:always,logged_in,admin',
		'template'   => 'required_if:type,database',
		'file'       => 'required_if:type,filesystem',
	];

	/**
	 * The theme bag which is used for rendering file-based pages.
	 *
	 * @var \Illuminate\View\Environment
	 */
	protected $themeBag;

	/**
	 * The theme in which we render pages.
	 *
	 * @var string
	 */
	protected $theme = null;

	/**
	 * The group model.
	 *
	 * @var string
	 */
	protected $groupModel = 'Platform\Users\Models\Group';

	/**
	 * The menu model.
	 *
	 * @var string
	 */
	protected $menuModel = 'Platform\Menus\Models\Menu';

	/**
	 * Constructor.
	 *
	 * @param  string  $model
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct($model, Dispatcher $dispatcher)
	{
		$this->model = $model;

		$this->dispatcher = $dispatcher;
	}

	/**
	 * {@inheritDoc}
	 */
	public function grid()
	{
		return $this
			->createModel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll()
	{
		return $this
			->createModel()
			->newQuery()
			->get();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAllEnabled()
	{
		return $this
			->createModel()
			->newQuery()
			->where('enabled', 1)
			->get();
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		return $this
			->createModel()
			->orWhere('slug', $id)
			->orWhere('uri', $id)
			->orWhere('id', (int) $id)
			->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findEnabled($id)
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
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $data)
	{
		return $this->validatePage($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
		$page = $this->find($id);

		$this->rules['slug'] .= ",slug,{$page->slug},slug";

		$this->rules['uri'] .= ",uri,{$page->uri},uri";

		return $this->validatePage($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $data)
	{
		with($page = $this->createModel())->fill($data)->save($data);

		$this->setPageMenu($page, $data);

		$this->dispatcher->fire('platform.page.created', $page);

		return $page;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $data)
	{
		$page = $this->find($id);

		$page->fill($data)->save($data);

		$this->setPageMenu($page, $data);

		$this->dispatcher->fire('platform.page.updated', $page);

		return $page;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($page = $this->find($id))
		{
			$this->dispatcher->fire('platform.page.deleted', $page);

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
				$result = $this->getThemeBag()->getViewEnvironment()->inject($page->section, $value);

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
		$extensions = array_keys(View::getExtensions());

		$paths = array_filter(array_map(function($path) {

			// Full path to the pages folder
			$fullPath = implode(DIRECTORY_SEPARATOR, [$path, 'pages']);

			// Check if the path exists
			$pathConfig = head(Config::get('cartalyst/themes::paths'));

			$searchPath = str_replace($pathConfig, '', $path);

			if (is_dir($fullPath) && strpos($searchPath, 'admin') == false)
			{
				return $fullPath;
			}

		}, $this->getThemeBag()->getCascadedViewPaths($this->getTheme())));

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
		$extensions = array_keys(View::getExtensions());

		$paths = array_filter(array_map(function($path)
		{
			$pathConfig = head(Config::get('cartalyst/themes::paths'));

			$searchPath = str_replace($pathConfig, '', $path);

			if (strpos($searchPath, 'admin') == false)
			{
				return $path;
			}
		}, $this->getThemeBag()->getCascadedViewPaths($this->getTheme())));

		$finder = new Finder;
		$finder->in($paths);
		$finder->depth('< 3');
		$finder->exclude(Config::get('platform/pages::exclude'));
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
		return $this->themeBag;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setThemeBag(ThemeBag $themeBag)
	{
		$this->themeBag = $themeBag;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTheme()
	{
		return $this->theme;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setTheme($theme)
	{
		$this->theme = $theme;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getGroupModel()
	{
		return $this->groupModel;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setGroupModel($model)
	{
		$this->groupModel = $model;

		return $this;
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
	 * Validates a page.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	protected function validatePage($data)
	{
		$validator = Validator::make($data, $this->rules);

		$validator->passes();

		return $validator->errors();
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
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
