<?php namespace Platform\Pages;
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

class SettingsServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$this->app['platform.settings']->section('pages', function($section)
		{
			$section->name = 'Pages';

			$section->fieldset('general', function($fieldset)
			{
				$fieldset->name = 'General';

				#
				$repository = app('Platform\Pages\Repositories\PageRepositoryInterface');
				$allPages = $repository->findAll();


				$fieldset->field('default_page', function($field) use ($allPages)
				{
					$field->name = 'Default Page';
					$field->config = 'platform/pages::default_page';
					$field->info = 'The page that is shown on the root route.';

					foreach ($allPages as $page)
					{
						$field->option($page->slug, function($option) use ($page)
						{
							$option->value = $page->slug;
							$option->label = $page->name;
						});
					}
				});

				$fieldset->field('default_section', function($field)
				{
					$field->name = 'Default Section';
					$field->config = 'platform/pages::default_section';
					$field->info = 'The default section when using the database storage type.';
				});

				$fieldset->field('default_template', function($field) use ($repository)
				{
					$field->name = 'Default Template';
					$field->config = 'platform/pages::default_template';
					$field->info = 'The default template that is used for pages.';

					foreach ($repository->templates() as $value => $label)
					{
						$field->option($value, function($option) use ($value, $label)
						{
							$option->value = $value;
							$option->label = $label;
						});
					}
				});

				$fieldset->field('not_found', function($field) use ($allPages)
				{
					$field->name = '404 Error Page';
					$field->config = 'platform/pages::not_found';
					$field->info = 'The page that is shown when a 404 error arises.';

					$field->option(null, function($option)
					{
						$option->value = null;
						$option->label = 'Default';
					});

					foreach ($allPages as $page)
					{
						$field->option($page->slug, function($option) use ($page)
						{
							$option->value = $page->slug;
							$option->label = $page->name;
						});
					}
				});
			});
		});

	}
}
