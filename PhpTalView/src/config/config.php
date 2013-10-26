<?php

function template_id() {
	if (defined('TEMPLATE_ID')) {
		return TEMPLATE_ID.'/';
	}
	return '';
}

return array(
	'extension' => 'html',
	'preFilters' => array(),
	'postFilters' => array(),
	'encoding' => 'UTF-8',
	'outputMode' => PHPTAL::HTML5,
	'phpCodeDestination' => storage_path('views/').template_id(),
	'forceReparse' => true,
	'templateRepository' => app_path('views/').template_id()
);
