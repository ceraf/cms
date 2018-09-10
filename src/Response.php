<?php

namespace App;

class Response
{
	const HEADER_TITLE = [
		'200' => 'HTTP/1.0 200 OK',
		'404' => 'HTTP/1.0 404 Not Found'
	];	
	
	private $view;
	private $headers;
	
	public function __construct ($view, $header = '200')
	{
		$this->view = $view;
		$this->headers = [self::HEADER_TITLE[$header]];
	}
	
	public function getView()
	{
		return $this->view;
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
}
