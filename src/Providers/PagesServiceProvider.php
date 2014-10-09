<?php namespace Platform\Pages\Providers;
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

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Platform\Pages\Repositories\IlluminatePageRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PagesServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$this->package('platform/pages', 'platform/pages'. __DIR__.'/../..');

		// Register the attributes namespace
		$this->app['platform.attributes']->registerNamespace(
			$this->app['Platform\Pages\Models\Page']
		);

		// Check the environment and app.debug settings
		if ($this->app->environment() === 'production' or $this->app['config']['app.debug'] === false)
		{
			$notFound = $this->app['config']['platform/pages::not_found'];

			if ( ! is_null($notFound))
			{
				$this->app->error(function(NotFoundHttpException $exception, $code) use ($notFound)
				{
					$this->app['log']->error($exception);

					try
					{
						$repository = $this->app['Platform\Pages\Repositories\PageRepositoryInterface'];

						$content = $repository->find($notFound);

						return Response::make($repository->render($content), 404);
					}
					catch (Exception $e)
					{

					}
				});
			}
		}

		// Register the menu page type
		$this->app['Platform\Menus\Models\Menu']->registerType($this->app['Platform\Menus\Types\PageType']);

		// Subscribe the registered event handlers
		$this->app['events']->subscribe('Platform\Pages\Handlers\PageEventHandlerInterface');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		$this->registerPagesValidator();

		$this->registerPageRepository();

		$this->registerEventHandlers();

		$this->app['Platform\Menus\Types\PageType'] = $this->app->share(function($app)
		{
			return new \Platform\Pages\Menus\PageType($app['url'], $app['view'], $app['translator']);
		});
	}

	/**
	 * Register the pages validator.
	 *
	 * @return void
	 */
	protected function registerPagesValidator()
	{
		$binding = 'Platform\Pages\Validator\PagesValidatorInterface';

		if ( ! $this->app->bound($binding))
		{
			$this->app->bind($binding, 'Platform\Pages\Validator\PagesValidator');
		}
	}

	/**
	 * Register the page repository.
	 *
	 * @return void
	 */
	protected function registerPageRepository()
	{
		$pageRepository = 'Platform\Pages\Repositories\PageRepositoryInterface';

		$this->app->bindIf($pageRepository, function($app)
		{
			$model = get_class($app['Platform\Pages\Models\Page']);

			return (new IlluminatePageRepository($model, $app['events'], $app['cache']))
				->setThemeBag($app['themes'])
				->setTheme($app['config']['cartalyst/themes::active'])
				->setValidator($app['Platform\Pages\Validator\PagesValidatorInterface']);
		});
	}

	/**
	 * Register the event handlers.
	 *
	 * @return void
	 */
	protected function registerEventHandlers()
	{
		$this->app->bindIf(
			'Platform\Pages\Handlers\PageEventHandlerInterface',
			'Platform\Pages\Handlers\PageEventHandler'
		);
	}

}
