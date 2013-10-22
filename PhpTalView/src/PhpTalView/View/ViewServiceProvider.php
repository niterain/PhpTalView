<?php namespace PhpTalView\View;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider {

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
		$this->package('niterain/PhpTalView');

		$this->app['PhpTalView'] = $this->app->share(function($app)
			{
				return new \PhpTalView\View($this->app);
			});

		$this->app->booting(function() {
			$loader = AliasLoader::getInstance();
			$loader->alias('View', 'PhpTalView\Facades\View');
		});
	}

}