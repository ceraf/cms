<?php

namespace App;

use App\Request;
use App\Response;

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
        $route = $this->findAction();
        $controllername = '\\App\\Controllers\\' .	ucfirst($route['controller']).'Controller';
        $this->response = (new $controllername($this->request))->index();
        
        return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

	private function findAction()
	{
		$params = explode('/', $this->request->getServer('REQUEST_URI'));
		$controller = $params[1] ?? null;
		$action = $params[2] ?? 'index';
		
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
