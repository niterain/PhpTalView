<?php

return array(
	'extension' => 'html',
	'preFilters' => array(),
	'postFilters' => array(),
	'encoding' => 'UTF-8',
	'outputMode' => PHPTAL::HTML5,
	'phpCodeDestination' => storage_path('/views/'),
	'forceReparse' => true,
	'templateRepository' => app_path('/views/')
);
