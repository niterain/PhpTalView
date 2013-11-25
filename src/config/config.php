<?php

return array(
	'engine' => 'phptal',
	'extension' => 'html',
	'preFilters' => array(),
	'postFilters' => array(),
	'encoding' => 'UTF-8',
	'outputMode' => PHPTAL::HTML5,
	'phpCodeDestination' => storage_path().'/views/',
	'forceReparse' => true,
	'templateRepository' => app_path().'/views/'.TEMPLATE_ID,
	'translationClass' => '\PHPTAL_GetTextTranslator', // 'Niterain\PhpTalView\Translator',
	'translationPath' => app_path().'/lang/',
	'translationFilename' => 'trans',
	'translationLanguages' => array('en_US.utf8'),
);
