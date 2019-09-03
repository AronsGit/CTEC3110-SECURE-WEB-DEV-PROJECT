<?php


// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(
        $container['settings']['view']['template_path'],
        $container['settings']['view']['twig'],
        [
            'debug' => true // This line should enable debug mode
        ]
    );
    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    // This line should allow the use of {{ dump() }} debugging in Twig
    $view->addExtension(new \Twig_Extension_Debug());
    return $view;
};


$container['mysql_model'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'MySqlModel.php';
    $model = new MySqlModel();
    return $model;
};
$container['mysql_queries'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'MySqlQueries.php';
    $model = new MySqlQueries();
    return $model;
};
$container['mysql_wrapper'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'MySqlWrapper.php';
    $model = new MySqlWrapper();
    return $model;
};
$container['profile_model'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'ProfileModel.php';
    $model = new ProfileModel();
    return $model;
};
$container['soap_model'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'SoapModel.php';
    $model = new SoapModel();
    return $model;
};
$container['session_model'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'SessionModel.php';
    $model = new SessionModel();
    return $model;
};
$container['session_wrapper'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'SessionWrapper.php';
    $session_wrapper = new SessionWrapper();
    return $session_wrapper;
};
$container['sanitised_validator'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'SanitisedValidator.php';
    $validator = new SanitisedValidator();
    return $validator;
};
$container['xml_parser'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'XmlParser.php';
    $model = new XmlParser();
    return $model;
};
$container['base64_wrapper'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'Base64Wrapper.php';
    $model = new Base64Wrapper();
    return $model;
};
$container['bcrypt_wrapper'] = function ($container) {
    $class_path = $container->get('settings')['class_path'];
    require $class_path . 'BcryptWrapper.php';
    $model = new BcryptWrapper();
    return $model;
};

$container['dbase'] = function ($container) {
    $db_conf = $container['settings']['pdo'];
    $host_name = $db_conf['rdbms'] . ':host=' . $db_conf['host'];
    $port_number = ';port=' . '3306';
    $user_database = ';dbname=' . $db_conf['db_name'];
    $host_details = $host_name . $port_number . $user_database;
    $user_name = $db_conf['user_name'];
    $user_password = $db_conf['user_password'];
    $pdo_attributes = $db_conf['options'];
    $obj_pdo = null;
    try
    {
        $obj_pdo = new PDO($host_details, $user_name, $user_password, $pdo_attributes);
    }
    catch (PDOException $exception_object)
    {
        trigger_error('error connecting to  database');
    }
    return $obj_pdo;
};