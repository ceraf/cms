<?php

namespace App;

class Core
{
    private static $instance;
    private $data;

    private function __construct() {}
    
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function setEntityManager($value)
    {
        $this->data['em'] = $value;
        return $this;
    }
    
    public function getEntityManager()
    {
        return $this->data['em'];
    }
    
    public function setSession($name, $value)
    {
        $_SESSION[$name] = $value;
    }
    
    public function getSession($name)
    {
        return $_SESSION[$name] ?? null;
    }
	
	public function clearSession($name)
	{
		unset($_SESSION[$name]);
	}
}
