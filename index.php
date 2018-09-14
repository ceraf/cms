<?php

define ('PROJECT_DIR', __DIR__);
define ('LAYOUT_DIR', PROJECT_DIR.'/layout');
define ('CONTROLLER_DIR', PROJECT_DIR.'/controllers');

require __DIR__.'/vendor/autoload.php';

\App\Front::run();

