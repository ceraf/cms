<?php

define ('PROJECT_DIR', __DIR__.'/..');
define ('LAYOUT_DIR', PROJECT_DIR.'/layout');
define ('CONTROLLER_DIR', PROJECT_DIR.'/controllers');

require __DIR__.'/../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = array("/path/to/entity-files");
$isDevMode = false;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '12345',
    'dbname'   => 'test_task',
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);
\App\Core::getInstance()->setEntityManager($entityManager);

\App\Front::run();

