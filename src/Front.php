<?php

namespace App;

use App\Request;
use App\Application;
use App\Response;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Front
{
    private function __construct() {}
    
    public static function run()
    {
        $instance = new self();
        $instance->init();
        $instance->processRrequest();
    }
    
    private function init()
    {
		$dbParams = null;
		if (file_exists(PROJECT_DIR.'/config/config.php'))
			include (PROJECT_DIR.'/config/config.php');
		
		$paths = array("/path/to/entity-files");
		$isDevMode = false;
		try {
			$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
			$entityManager = EntityManager::create($dbParams, $config);
			\App\Core::getInstance()->setEntityManager($entityManager);
            $entityManager->find('\App\Models\Setting', 123);
		} catch (\Exception $e) {
			\App\Core::getInstance()->setEntityManager(null);
		}

    }
    
    private function processRrequest()
    {
        $request = new Request();
        $application = new Application($request);
        $application->exec();
        $this->invokeView($application->getResponse());
    }
    
    private function invokeView(Response $response)
    {
		foreach ($response->getHeaders() as $title) {
			header($title);
		}
        $params = $response->getParams();
        ob_start();
        include(LAYOUT_DIR . '/' . $response->getView().'.phtml');
        $content = ob_get_contents();
        ob_end_clean();

		include(LAYOUT_DIR . '/layout.phtml');
		exit;
    }
}
