<?php namespace PhpTalView\View\Engines;

use Illuminate\View\Engines\EngineInterface;
use PhpTalView\View\FilterChain;

class PhpTalEngine implements EngineInterface {
	protected $phptal;
	protected $app;

	/**
	 * Prep the PHPTAL object
	 *
	 * @param $app
	 */
	public function __construct($app) {
		$this->app = $app;
		$this->config = $app['config'];

		$this->phptal = new \PHPTAL();

		// Override the defaults with information from config file
		$preFilters = $this->ifNotEmpty($this->config->get('PhpTalView::preFilters'), array());
		$postFilters = $this->ifNotEmpty($this->config->get('PhpTalView::postFilters'), array());
		$outputMode = $this->ifNotEmpty($this->config->get('PhpTalView::outputMode'), 'UTF-8');
		$phpCodeDestination = $this->ifNotEmpty($this->config->get('PhpTalView::phpCodeDestination'), $app['path.storage'].'/views') ;
		$forceReparse = $this->ifNotEmpty($this->config->get('PhpTalView::forceParse'), true);
		$templateRepository = $this->ifNotEmpty($this->config->get('PhpTalView::templateRepository'), $app['path'].'/views');

		// Setting up all the filters

		if (!empty($preFilters)) {
			foreach ($preFilters as $filter) {
				$this->phptal->addPreFilter($filter);
			}
		}

		if (!empty($postFilters)) {
			$filterChain = new FilterChain();
			foreach ($postFilters as $filter) {
				$filterChain->add($filter);
			}
			$this->phptal->setPostFilter($filterChain);
		}

		$this->phptal->setForceReparse($forceReparse);
		$this->phptal->setOutputMode($outputMode);
		$this->phptal->setTemplateRepository($templateRepository);
		$this->phptal->setPhpCodeDestination($phpCodeDestination);
	}

	/**
	 * Save a few keystrokes on setting defaults
	 *
	 * @param $value
	 * @param $default
	 *
	 * @return mixed
	 */
	protected function ifNotEmpty($value, $default) {
		return (!empty($value) ? $value : $default);
	}

	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string $path
	 * @param  array $data
	 *
	 * @return string
	 */
	public function get($path, array $data = array())
	{
		if (!empty($data)) {
			foreach ($data as $field => $value) {
				if (!preg_match('/^_|\s/', $field)) {
					$this->phptal->set($field, $value);
				}
			}
		}
		$this->phptal->setTemplate($path);
		return $this->phptal->execute();
	}

}