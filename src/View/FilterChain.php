<?php namespace PhpTalView\View;

use PHPTAL_Filter;

class FilterChain implements PHPTAL_Filter {
	protected  $filters = array();

	public function add(PHPTAL_Filter $filter) {
		$this->filters[] = $filter;
	}

	public function filter($source) {
		foreach ($this->filters as $filter) {
			$source = $filter->filter($source);
		}
		return $source;
	}

}