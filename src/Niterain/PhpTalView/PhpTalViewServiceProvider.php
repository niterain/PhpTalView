<?php namespace Niterain\PhpTalView;

use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Environment;

use Niterain\PhpTalView\Engines\PhpTalEngine;

class PhpTalViewServiceProvider extends ViewServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		// This is only for the template system.
		$templateIdString = '';
		$templateIdsArray = $this->app['config']->get('template.templateIds');
		if (is_array($templateIdsArray) && !empty($templateIdsArray) && !empty($_SERVER['HTTP_HOST'])) {
			$templateIdString = array_search($_SERVER['HTTP_HOST'], $templateIdsArray);
			$templateIdString .= '/';
		}
		define('TEMPLATE_ID', $templateIdString);

		$this->package('niterain/PhpTalView', 'PhpTalView', __DIR__.'/../../');
		parent::register();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('phptalview');
	}

	/**
	 * Register Engine Resolver
	 *
	 * @return void
	 */
	public function registerEngineResolver()
	{
		$my = $this;
		$app = $this->app;

		$app['view.engine.resolver'] = $app->share(
			function ($app) use ($my) {
				$resolver = new EngineResolver();
				$my->{'registerPhpTalEngine'}($resolver);
				return $resolver;
			}
		);
	}

	/**
	 * Register Environment
	 *
	 * @return void
	 */
	public function registerEnvironment()
	{
		$this->app['view'] = $this->app->share(
			function ($app) {
				$resolver = $app['view.engine.resolver'];

				$resource = 'view.';
				$engine = $app['config']->get($resource.'engine');
				if (empty($engine)) {
					$resource = 'PhpTalView::';
				}

				$finder = new FileViewFinder($app['files'], array());
				$finder->addLocation($app['config']->get($resource.'templateRepository'));
				$finder->addExtension($app['config']->get($resource.'extension'));

				$env = new Environment($resolver, $finder, $app['events']);
				$env->addExtension($app['config']->get($resource.'extension'), 'phptal');
				$env->setContainer($app);
				$env->share('app', $app);
				return $env;
			}
		);
	}

	/**
	 * Register PhpTalEngine
	 *
	 * @param $resolver
	 *
	 * @return void
	 */
	public function registerPhpTalEngine(&$resolver)
	{
		$app = $this->app;

		$resolver->register(
			'phptal',
			function () use ($app) {
				return new PhpTalEngine($app);
			}
		);
	}

}