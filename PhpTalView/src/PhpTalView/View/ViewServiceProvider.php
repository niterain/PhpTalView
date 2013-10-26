<?php namespace PhpTalView\View;

use Illuminate\View\Engines\EngineResolver;

use Illuminate\View\Environment;
use PhpTalView\View\Engines\PhpTalEngine;

class ViewServiceProvider extends \Illuminate\View\ViewServiceProvider {

	/**
	 * Setting up the config log
	 */
	public function register() {
		$this->package('niterain/PhpTalView','PhpTalView', __DIR__.'/../../');
		parent::register();
	}

	/**
	 * Register Engine Resolver
	 *
	 * @return void
	 */
	public function registerEngineResolver() {
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
	public function registerEnvironment() {
		$this->app['view'] = $this->app->share(function($app) {
				$resolver = $app['view.engine.resolver'];
				$finder = $app['view.finder'];

				$finder = new \Illuminate\View\FileViewFinder($app['files'], array());
				$finder->addLocation($app['config']->get('PhpTalView::templateRepository'));
				$finder->addExtension($app['config']->get('PhpTalView::extension'));

				$env = new Environment($resolver, $finder, $app['events']);
				$env->addExtension($app['config']->get('PhpTalView::extension'),'phptal');
				$env->setContainer($app);
				$env->share('app', $app);
				return $env;
			});
	}

	/**
	 * Register PhpTalEngine
	 *
	 * @param $resolver
	 *
	 * @return void
	 */
	public function registerPhpTalEngine($resolver) {
		$app = $this->app;

		$resolver->register('phptal', function() use ($app) {
				return new PhpTalEngine($app);
			});
	}
}