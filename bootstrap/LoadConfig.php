<?php

//load route
require_once __DIR__ . '/../routers/api.php';

//加载config配置文件,所有config目录下的php文件都加载
$basePath = base_path('config');
$configs = scandir($basePath);

foreach ($configs as $file)
{
    if(substr($file,-4,4) == '.php'){
        $config = new \Xly\Config(require_once($basePath . '/' . $file ));
        \Xly\Register::set('config', $config->toArray());
    }
}
