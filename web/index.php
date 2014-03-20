<?php
$loader = require_once __DIR__ . '/../DFramework/vendor/autoload.php';
$loader->add('', __DIR__ . '/../app/src/');

use DFramework\HttpKernel\HttpKernel;

$app = new HttpKernel();
$app->run();