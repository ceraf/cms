<?php

namespace App;

use App\Request;
use App\Response;
use App\Core;

class Application
{
    private $request;
    private $response;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function exec()
    {
        session_start();
        $route = $this->findAction();
        $controllername = '\\App\\Controllers\\' .	ucfirst($route['controller']).'Controller';
        $method = $route['action'];
        $em = Core::getInstance()->getEntityManager();
        if ($em || !USE_DB)
            $this->response = (new $controllername($this->request))->$method();
        else
            $this->response = (new \App\Controllers\InstallController ($this->request))->index();
            
        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

	private function findAction()
	{
        $query = $this->request->getServer('REQUEST_URI');
        if (strpos($query, '?') !== false)
            $query = substr($query, 0, strpos($query, '?'));
		$params = explode('/', $query);
		$controller = (isset($params[1]) && $params[1]) ? $params[1] : 'index';
        $action = (isset($params[2]) && $params[2]) ? $params[2] : 'index';
        
		if (!($controller && class_exists('\\App\\Controllers\\' .	ucfirst($controller).'Controller'))) {
			$controller = 'Notfound';
			$action = 'index';
		} else {
            if (!in_array($action, get_class_methods('\\App\\Controllers\\' .	ucfirst($controller).'Controller'))) {
                $controller = 'Notfound';
                $action = 'index';
            }
        }

		return ['controller' => $controller, 'action' => $action];
	}
}
