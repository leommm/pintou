<?php
error_reporting(E_ERROR | E_PARSE);
defined('YII_DEBUG') or define('YII_DEBUG', true);
require __DIR__ . '/../vendor/autoload.php';
$app = new app\hejiang\Application();
$app->run();
