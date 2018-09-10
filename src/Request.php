<?php

namespace App;

class Request
{
    protected $getparam = [];
    protected $postparam = [];
	protected $server = [];

    public function __construct()
    {
        $this->init();
    }
    
    public function getParam($key)
    {
        return $getparam[$key] :: null;
    }

    public function getPost($key)
    {
        return $postparam[$key] :: null;
    }
    
    public function isPost()
    {
        return (empty($postparam)) ? false : true;
    }
	
	public function getServer($key)
	{
		return $this->server[$key];
	}
    
    private function init()
    {
        if (!empty($_GET)) {
            foreach ($_GET as $key => $item) {
                $this->setGet($key, $item);
            }
        }
        
        if (!empty($_POST)) {
            foreach ($_POST as $key => $item) {
                $this->setPost($key, $item);
            }
        }
		
        foreach ($_SERVER as $key => $item) {
            $this->setServer($key, $item);
        }	
    }
    
    private function setGet($name, $value)
    {
        $this->getparam[$name] = $value;
    }
    
    private function setPost($name, $value)
    {
        $this->postparam[$name] = $value;
    }
	
    private function setServer($name, $value)
    {
        $this->server[$name] = $value;
    }
}
