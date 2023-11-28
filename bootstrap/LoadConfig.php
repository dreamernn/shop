<?php

//load route
require_once __DIR__.'/../routers/api.php';

//loading config files,all of files under config dir
$basePath = base_path('config');
$configs  = scandir($basePath);

foreach ($configs as $file) {
    if (substr($file, -4, 4) == '.php') {
        $config = new \Xly\Config(require_once($basePath.'/'.$file));
        \Xly\Register::set('config', $config->toArray());
    }
}
