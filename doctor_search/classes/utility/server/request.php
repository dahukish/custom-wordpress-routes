<?php 

namespace Utility\Server;

class Request 
{
	private $uri = null;
	private $method = null;

	public static function getRequest()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		$uri = $_SERVER['REQUEST_URI'];

		return new static($method, $uri);
	}

	public function __construct($method, $uri)
	{
		$this->method = $method;
		$this->uri = $uri;
	}

	public function equalsHttpVerb($verb) 
	{
		return (strtolower($verb) === strtolower($this->method));
	}

	public function equalsUri($uri)
	{
		return (!(strpos($this->uri, $uri) === false));
	}

	public function getUri()
	{
		return $this->uri;
	} 

	public function getMethod()
	{
		return $this->method;
	}

	public function getToken($pattern)
	{
		if(!is_string($pattern)) return false;
		
		if(preg_match('/'.$pattern.'/i', $this->uri, $token_match)){
			return $token_match[count($token_match)-1];
		}

		return false;
	}
}