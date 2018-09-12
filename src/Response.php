<?php

namespace App;

class Response
{
	const HEADER_TITLE = [
		'200' => 'HTTP/1.0 200 OK',
		'404' => 'HTTP/1.0 404 Not Found'
	];	
	
	private $view;
	private $headers = [];
    private $params;
	
	public function __construct ($view, $params = null)
	{
		$this->view = $view;
        $this->params = $params;
		//$this->headers = [self::HEADER_TITLE[200]];
	}
	
	public function getView()
	{
		return $this->view;
	}
	
    public function setCode($code)
    {
        $this->headers = [self::HEADER_TITLE[$code]];
        return $this;
    }
    
	public function setHeader($title)
	{
		$this->headers[] = $title;
		return $this;
	}
	
	public function getHeaders()
	{
		return $this->headers;
	}
    
    public function getParams()
    {
        return $this->params;
    }
}
