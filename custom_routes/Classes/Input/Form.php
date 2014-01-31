<?php 

namespace Input;

use Utility\Server\Request;


class Form
{
	private $request_method = null;
	private $input = null;

	public static function getFormInput(Request $request)
	{
		return new static($request);
	}

	public function __construct(Request $request)
	{
		$this->request_method = $request->getMethod();
		$this->input = $this->parseInput();
	}

	public function parseInput()
	{	
		if($this->request_method === 'GET')
		{
			return $this->parseGET();
		}

		return $this->parsePOST();
	}

	private function parseGET()
	{
		return $this->validateInput($_GET);
	}

	private function parsePOST()
	{
		return $this->validateInput($_POST);
	}

	private function validateInput($input)
	{
		if(!empty($input)){
			return $input;
		}

		return false;
	}

	public function getValue($val)
	{
		if(isset($this->input[$val])&&!empty($this->input[$val]))
			return $this->keepItClean($val);
	
		return false;
	}
	
	private function keepItClean($key)
	{
		global $wpdb;
		
		return $wpdb->prepare(trim(strip_tags($this->input[$key])),'');
	}
}