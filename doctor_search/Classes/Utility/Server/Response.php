<?php 

namespace Utility\Server;

class Response
{
	private $header_status = null;
	private $template_path = null;
	private $template_data = null;

	public function __construct(){}

	public function setHeader($header) 
	{
		$this->header_status = $header;
	}

	public function setTemplate($path) 
	{
		$this->template_path = $path;
	}

	public function setTemplateData(array $data)
	{
		(!empty($data))&&($this->template_data = $data);
	}

	public function display()
	{
		global $view_data;
		if(!is_null($this->template_data)) $view_data = $this->template_data;
		header($this->header_status);
		load_template($this->template_path);
		die();
	}
}