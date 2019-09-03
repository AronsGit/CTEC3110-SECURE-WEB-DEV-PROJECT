<?php
/**
 * Created by PhpStorm.
 * User: p2442537
 * Date: 13/08/2019
 * Time: 09:29
 */
session_start();



require 'vendor/autoload.php';

$settings = require __DIR__ . '/app/settings.php';

$container = new \Slim\Container($settings);


require __DIR__ . '/app/dependencies.php';

$app = new \Slim\App($container);

require __DIR__ . '/app/routes.php';

$app->run();

