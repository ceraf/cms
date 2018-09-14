<?php

namespace App;

use App\Request;
use App\Core;

abstract class Controller
{
    protected $request;
    protected $user;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    protected function notFound()
    {
        return (new \App\Controllers\NotfoundController($this->request))->index();
    }
    
    protected function isAjax()
    {
        $server = $this->request->getServer();
        if (
            isset($server['HTTP_X_REQUESTED_WITH']) && 
            !empty($server['HTTP_X_REQUESTED_WITH']) &&
            (strtolower($server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        )
            return true;
        else
            return false;
    }
    
    protected function getEntityManager()
    {
        return Core::getInstance()->getEntityManager();
    }
    
	protected function setFlashMessage($type, $msg)
	{
		$str = Core::getInstance()->getSession('flash');
		$str[$type][] = $msg;
		Core::getInstance()->setSession('flash', $str);
	}
	
	protected function getFlashMessage()
	{
		$msg = Core::getInstance()->getSession('flash') ?? null;
		if ($msg)
			Core::getInstance()->clearSession('flash');
		return $msg;
	}
	
    protected function setSession($name, $value)
    {
        Core::getInstance()->setSession($name, $value);
    }
    
    protected function clearSession($name)
    {
        Core::getInstance()->clearSession($name);
    }
    
    protected function getSession($name)
    {
        return Core::getInstance()->getSession($name);
    }
	
	protected function redirect($route)
	{
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: ".$route);
		exit();
	}
}
