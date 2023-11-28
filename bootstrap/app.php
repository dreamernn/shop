<?php
define('CAN_LOG_TOKEN', date('YmdHis').mt_rand(10000, 99999).uniqid('mirror_api'));

set_include_path(implode(PATH_SEPARATOR, [
    realpath(dirname(__DIR__))."/library/",
    get_include_path(),
]));
require_once 'Xly/Autoloader.php';
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../app/Helpers/helpers.php';
// Autoload class
$loader = Xly\Autoloader::getInstance();
$loader->registerNamespaces([
    'App' => dirname(__DIR__).'/app/',
]);
$loader->init();

@$app_env = getenv('APP_ENV') ?: getopt('', ["env::"])['env'] ?: 'alpha';
$env = '.'.$app_env.'.env';

try {
    (new \Xly\Environment(__DIR__.'/../environment/'.$env))->load();
} catch (Thowable $e) {
    exit("no environment file");
}

require_once 'LoadConfig.php';

spl_autoload_register(function ($className) {
    $libraryPath = __DIR__.'/../package/';
    $file        = $libraryPath.DIRECTORY_SEPARATOR.str_replace('\\', '/', $className).'.php';

    is_file($file) ? include($file) : '';
});




