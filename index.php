<?php

define ('PROJECT_DIR', __DIR__);
define ('LAYOUT_DIR', PROJECT_DIR.'/layout');
define ('CONTROLLER_DIR', PROJECT_DIR.'/controllers');

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);
\App\Core::getInstance()->setEntityManager($entityManager);

\App\Front::run();

