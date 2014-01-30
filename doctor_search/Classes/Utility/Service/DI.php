<?php 

namespace Utility\Service;

class DI extends \Pimple 
{
	public function get(array $inject) 
	{
		if(!empty($inject)) {
			$deps = array();
			foreach ($inject as $di) {
				$dep = $this[$di];
				if(!is_null($dep)) $deps[$di] = $dep; 
			}
			return $deps;
		}
	}
}