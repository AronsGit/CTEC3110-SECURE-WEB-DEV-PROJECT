<?php
/**
 * Created by PhpStorm.
 *User: slim
 *Date: 13/08/19
 *Time: 10:37
 */

//  Development: change to - on, on
ini_set('display_errors', 'on');
ini_set('html_errors', 'on');

define('m2m_username' , '18p2442537');
define('m2m_password' , 'Eem2mconnect233');
define('m2m_destination' , '+447817814149');
define('group_denomination', 'p2442537');


define('DIRSEP', DIRECTORY_SEPARATOR);

$url_root = $_SERVER['SCRIPT_NAME'];
$url_root = implode('/', explode('/', $url_root, -1));
$css_path = $url_root . '/css/standard.css';
$session_path = $url_root;
define('CSS_PATH', $css_path);
define ('SESSION_PATH', $session_path);
define('ROOT_PATH', $url_root);
define ('APP_NAME', 'Project');
define('LANDING_PAGE', $_SERVER['SCRIPT_NAME']);

define ('BCRYPT_ALGO', PASSWORD_DEFAULT);
define ('BCRYPT_COST', 12);


$settings = [
    "settings" => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'mode' => 'release',
        'debug' => true,
        'class_path' =>__DIR__. '/src/',
        'view' => [
        'template_path' =>__DIR__. '/templates',
        'twig' => [
            'cache' =>__DIR__.'/cache/twig',
            'auto_reload' => true,
        ]],
    'pdo' => [
        'rdbms' => 'mysql',
        'host' => 'localhost',
        'db_name' => 'project_db',
        'port' => '3306',
        'user_name' => 'root',
        'user_password' => 'root',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'options' => [
            PDO::ATTR_ERRMODE            =>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   =>true,

        ],
    ]
    ],
];

return $settings;
