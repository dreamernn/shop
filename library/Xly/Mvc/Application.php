<?php
namespace Xly\Mvc;

use Xly\Mvc\Http\Common;
use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;

use Xly\Mvc\Route;
use Xly\Mvc\Template;
use Xly\Mvc\Dispatcher;

class Application {

    const APPLICATION_DIR = 'app/';
    const CONTROLLER_DIR = 'Http/Controllers/';
    const MIDDLEWARE_DIR = 'Http/Middleware/';

    protected $_request = null;

    protected $_response = null;
   
    protected $_route = null;


    protected $_dispatcher = null;

    protected $_basePath = null;   

    public function __construct($basePath) {

        if (! empty(env('APP_TIMEZONE'))) {
            date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));
        }

        $this->_basePath = rtrim($basePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    public function init() {
        //define input and output
        $this->_request = new Request();
        $this->_response = new Response();

        $this->_route = $this->buildRoute();

        //build dispatcher
        $this->_dispatcher = $this->buildDispatcher();

        return $this;
    }

    private function buildRoute() {
        $route = new Route\Simple;
        return $route;
    }

    private function buildDispatcher() {
        $controllerBaseDir = $this->_basePath.self::APPLICATION_DIR.self::CONTROLLER_DIR;
        $middlewareBaseDir = $this->_basePath.self::APPLICATION_DIR.self::MIDDLEWARE_DIR;
        $dispatcher = new Dispatcher($controllerBaseDir,
                                     $middlewareBaseDir,
                                     $this->_route, 
                                     $this->_request, 
                                     $this->_response); 
        return $dispatcher;
    }

    /**
    * start to run application
    */
    public function run() {
        $response = $this->_dispatcher->run($this->_request, $this->_response);
        $response->sendHeaders();
        
        return $response->getReturn();
    }

}//END OF CLASS
