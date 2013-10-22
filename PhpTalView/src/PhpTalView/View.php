<?php namespace PhpTalView;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

class View extends  \Illuminate\View\View {
	const VERSION = '0.1.0';
	protected $app;
	protected $extension;
	protected $phptal;

	public function __construct(Application $app) {
		// Override the defaults with information from config file

		$this->extension = $extension = Config::get('PhpTalView::extension');

		$preFilters = $this->ifNotEmpty(Config::get('PhpTalView::preFilters'), array());
		$postFilters = $this->ifNotEmpty(Config::get('PhpTalView::postFilters'), array());
		$outputMode = $this->ifNotEmpty(Config::get('PhpTalView::outputMode'), 'UTF-8');
		$phpCodeDestination = $this->ifNotEmpty(Config::get('PhpTalView::phpCodeDestination'), $app['path.storage'].'/views') ;
		$forceReparse = $this->ifNotEmpty(Config::get('PhpTalView::forceParse'), true);
		$templateRepository = $this->ifNotEmpty(Config::get('PhpTalView::templateRepository'), $app['path'].'/views');

		$this->app = $app;
		$this->phptal = new \PHPTAL();

		// Setting up all the filters

		if (!empty($preFilters)) {
			foreach ($preFilters as $filter) {
				$this->phptal->addPreFilter($filter);
			}
		}

		if (!empty($postFilters)) {
			$filterChain = new FilterChain();
			foreach ($postFilters as $filter) {
				$this->phptal->add($filter);
			}
			$this->phptal->setPostFilter($filterChain);
		}

		$this->phptal->setForceReparse($forceReparse);
		$this->phptal->setOutputMode($outputMode);
		$this->phptal->setTemplateRepository($templateRepository);
		$this->phptal->setPhpCodeDestination($phpCodeDestination);
	}

	protected function ifNotEmpty($value, $default) {
		return (!empty($value) ? $value : $default);
	}

	public function __get($key)
	{
		return $this->phptal->get($key);
	}

	public function __set($key, $value)
	{
		$this->php->set($key, $value);
	}

	public function getExtension() {
		return $this->extension;
	}

	public function setExtension($extension) {
		$this->extension = $extension;
	}

	public function getPhpTalView() {
		return new \PhpTalView\Environment();
	}

	public function make($template, $data = array()) {
		if (!empty($data)) {
			foreach ($data as $field => $value) {
				$this->phptal->set($field, $value);
			}
		}
		$this->phptal->setTemplate($template.((strpos($this->extension, '.') > -1) ? $this->extension : '.'.$this->extension));
		return $this->phptal->execute();
	}

}