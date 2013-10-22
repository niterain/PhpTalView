<?php namespace PhpTalView\Facades;

use Illuminate\Support\Facades\Facade;

class View extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'PhpTalView';
	}

}