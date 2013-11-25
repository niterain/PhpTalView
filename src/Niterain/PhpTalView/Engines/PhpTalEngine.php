<?php namespace Niterain\PhpTalView\Engines;

use Illuminate\View\Engines\EngineInterface;
use Niterain\PhpTalView\PhpTalFilterChain;
use Niterain\PhpTalView\Translator;

class PhpTalEngine implements EngineInterface {
	protected $phptal;
	protected $app;
	protected $config;
	protected $translationSettings = array();

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
		$resource = 'PhpTalView::';
		$engine = $this->config->get('view.default');
		if (!empty($engine) && $engine == 'phptal') {
			$resource = 'view.';
		}
		
		$preFilters = $this->config->get($resource.'preFilters', array());
		$postFilters = $this->config->get($resource.'postFilters', array());
		$encoding = $this->config->get($resource.'encoding', 'UTF-8');
		$outputMode = $this->config->get($resource.'outputMode', 55);
		$phpCodeDestination = $this->config->get($resource.'phpCodeDestination', $app['path.storage'].'/views') ;
		$forceReparse = $this->config->get($resource.'forceParse', true);
		$templateRepository = $this->config->get($resource.'templateRepository', $app['path'].'/views');
		$translationClass = $this->config->get($resource.'translationClass');
		$translationLanguages = $this->config->get($resource.'translationLanguages', array('en'));
		$translationFilename = $this->config->get($resource.'translationFilename', 'translations');
		$translationPath = $this->config->get($resource.'translationPath', app_path().'/lang/');

		// Setting up translation settings
		$this->translationSettings['encoding'] = $encoding;
		$this->translationSettings['path'] = $translationPath;
		$this->translationSettings['languages'] = $translationLanguages;

		if (!empty($translationClass)) {
			$this->setTranslator($translationLanguages, $translationFilename, $translationClass);
		}
		// Setting up all the filters

		if (!empty($preFilters)) {
			foreach ($preFilters as $filter) {
				$this->phptal->addPreFilter($filter);
			}
		}

		if (!empty($postFilters)) {
			$filterChain = new PhpTalFilterChain();
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
	 * Sets the translator
	 *
	 * @param null $language
	 * @param string $domain
	 * @param string $translatorClass
	 */
	public function setTranslator($languages = null, $domain = 'translations', $translatorClass = '\PHPTAL_GetTextTranslator') {
		if ($languages === null) {
			$languages = array($this->config->get('app.locale'));
		}

		$translator = new $translatorClass;

		call_user_func_array(array($translator, 'setLanguage'), $languages);
		$translator->setEncoding($this->translationSettings['encoding']);
		$translator->addDomain($domain, $this->translationSettings['path']);

		$this->phptal->setTranslator($translator);
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
