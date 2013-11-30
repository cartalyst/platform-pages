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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Config;
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
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $rules = array(
		'name'       => 'required|max:255',
		'slug'       => 'required|max:255|unique:pages',
		'uri'        => 'required|max:255|unique:pages',
		'enabled'    => 'required',
		'type'       => 'required|in:database,filesystem',
		'visibility' => 'required|in:always,logged_in,admin',
		'template'   => 'required_if:type,database',
		'file'       => 'required_if:type,filesystem',
	);

	/**
	 * Start it up.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function __construct($model)
	{
		$this->model = $model;
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
		return $this->createModel()->newQuery()->get();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAllEnabled()
	{
		return $this->createModel()->newQuery()->where('enabled', 1)->get();
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		return $this->createModel()->orWhere('uri', $id)->orWhere('slug', $id)->orWhere('id', $id)->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findEnabled($id)
	{
		return $this->createModel()->orWhere('uri', $id)->orWhere('slug', $id)->orWhere('id', $id)->where('enabled', 1)->first();
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
		return $this->validatePage($data, $id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $data)
	{
		with($model = $this->createModel())->fill($data)->save($data);

		return $model;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $data)
	{
		$model = $this->find($id);

		$model->fill($data)->save($data);

		return $model;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($model = $this->find($id))
		{
			$model->delete();

			return true;
		}

		return false;
	}

	/**
	 * Validates a page.
	 *
	 * @param  array  $data
	 * @param  mixed  $id
	 * @return \Illuminate\Support\MessageBag
	 */
	protected function validatePage($data, $id = null)
	{
		$rules = $this->rules;

		if ($id)
		{
			$model = $this->find($id);

			$rules['slug'] .= ",slug,{$model->slug},slug";
			$rules['uri'] .= ",uri,{$model->uri},uri";
		}

		$validator = Validator::make($data, $rules);

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
	 * Returns a list of the available page files on the current active theme.
	 *
	 * @return array
	 */
	public function files()
	{
		$model = $this->model;

		$extensions = array_keys(View::getExtensions());

		$paths = array_filter(array_map(function($path) {

			// Full path to the pages folder
			$fullPath = implode(DIRECTORY_SEPARATOR, array($path, 'pages'));

			// Check if the path exists
			if (is_dir($fullPath))
			{
				return $fullPath;
			}

		}, $model::getThemeBag()->getCascadedViewPaths($model::getTheme())));

		$finder = with(new Finder)->files()->in($paths);

		$files = array();

		// Replace all file extensions with nothing. pathinfo()
		// won't tackle ".blade.php" so this is our best shot.
		$replacements = array_pad(array(), count($extensions), '');

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
	 * Returns a list of the available templates of the current active theme.
	 *
	 * @return array
	 */
	public function templates()
	{
		$model = $this->model;

		$extensions = array_keys(View::getExtensions());

		$finder = new Finder;
		$finder->in($model::getThemeBag()->getCascadedViewPaths($model::getTheme()));
		$finder->depth('< 3');
		$finder->exclude(Config::get('platform/pages::exclude'));
		$finder->name(sprintf(
			'/.*?\.(%s)/',
			implode('|', array_map(function($extension)
			{
				return preg_quote($extension, '/');
			}, $extensions))
		));

		$files = array();

		// Replace all file extensions with nothing. pathinfo()
		// won't tackle ".blade.php" so this is our best shot.
		$replacements = array_pad(array(), count($extensions), '');

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

}
