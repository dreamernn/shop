<?php

namespace Xly\Mvc;

use Xly\Mvc\Route\AbstractRoute;

use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;
use Xly\Mvc\Http\Common;
use Xly\RouterRegister;
use Common\Logger;

class Dispatcher {

    const URI_CONTROLLER_SEPARATOR = '@';

    const URI_CONTROLLER_ACTION = 'action';

    const MIDDLEWARE_KEYWORD = 'middleware';

    const MIDDLEWARE_SUFFIX = 'Middleware';

    const MIDDLEWARE_BEFORE_RUN = 'before';

    const MIDDLEWARE_AFTER_RUN = 'after';

    const DEFAULT_CONTROLLER_NAMESPACE = '\App\Http\Controllers\\';

    const DEFAULT_MIDDLEWARE_NAMESPACE = '\App\Http\Middleware\\';

    private $_controllerDirectory = "";

    private $_middlewareDirectory = "";

    private $_route = null;

    private $_request = null;

    private $_response = null;

    /**
     * Create a dispatcher instance
     *
     * @param string                      $controllerDirectory
     * @param Xly\Mvc\Route\AbstractRoute $route
     * @param Xly\Mvc\Http\Request        $request
     * @param Xly\Mvc\Http\Response       $response
     */
    public function __construct($controllerDirectory, $middlewareBaseDir, AbstractRoute $route, Request $request, Response $response) {
        $this->setControllerDirectory($controllerDirectory);
        $this->setMiddlewareDirectory($middlewareBaseDir);
        $this->setRoute($route);
        $this->setRequest($request);
        $this->setResponse($response);
    }

    /**
     * Get the root directory of all the controllers
     *
     * @return string
     */
    public function getControllerDirectory() {
        return $this->_controllerDirectory;
    }

    /**
     * Set the root directory of all the controllers
     *
     * @param string $dir
     *
     * @return
     */
    public function setControllerDirectory($dir) {
        $dir                        = (string)$dir;
        $dir                        = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $this->_controllerDirectory = $dir;
    }

    /**
     * Get the root directory of all the middleware
     *
     * @return string
     */
    public function getMiddlewareDirectory() {
        return $this->_middlewareDirectory;
    }

    /**
     * Set the root directory of all the middleware
     *
     * @param string $dir
     *
     * @return
     */
    public function setMiddlewareDirectory($dir) {
        $dir                        = (string)$dir;
        $dir                        = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $this->_middlewareDirectory = $dir;
    }


    /**
     * Get the associated route
     *
     * @return Xly\Mvc\Route\AbstractRoute
     */
    public function getRoute() {
        return $this->_route;
    }

    /**
     * Set the associated route
     *
     * @param Xly\Mvc\Route\AbstractRoute $route
     *
     * @return
     */
    public function setRoute(AbstractRoute $route) {
        $this->_route = $route;

        return;
    }

    /**
     * Get the request associated with the template
     *
     * @return Xly\Mvc\Http\Request
     */
    public function getRequest() {
        return $this->_request;
    }

    /**
     * Set the request associated with the template
     *
     * @param Xly\Mvc\Http\Request $request
     *
     * @return
     */
    public function setRequest(Request $request) {
        $this->_request = $request;

        return;
    }

    /**
     * Get the response associated with the template
     *
     * @return Xly\Mvc\Http\Response
     */
    public function getResponse() {
        return $this->_response;
    }

    /**
     * Set the response associated with the template
     *
     * @param Xly\Mvc\Http\Response $response
     *
     * @return
     */
    public function setResponse(Response $response) {
        $this->_response = $response;

        return;
    }

    /**
     * Run the action according to the request
     *
     * @return Xly\Mvc\Http\Reponse
     */
    public function run() {
        $route   = $this->getRoute();
        $request = $this->getRequest();
        $route->match($request);
        $uri      = $route->getUri();
        $routeArr = RouterRegister::getRouter($uri);

        if (empty($routeArr)) {
            throw new Dispatcher\Exception("The Request:".$uri." is not Found  ", Common::SC_NOT_FOUND);
        }

        if (!empty($routeArr['route_params'])) {
            $request->setParam('routeParams', $routeArr['route_params'] ?? []);
        }

        $this->_checkRequestMothed($uri, $routeArr);

        $sureUriArr = explode(self::URI_CONTROLLER_SEPARATOR, $routeArr[self::URI_CONTROLLER_ACTION]);

        $this->_beforeRunUri($routeArr);

        $response = $this->_run($sureUriArr[0], $sureUriArr[1]);

        $this->_afterRunUri($routeArr);

        //handle error
        if ($response->isExceptional()) {
            throw $response->getException();
        }

        return $response;
    }

    private function _beforeRunUri($routeArr) {
        $this->_runMiddlewares($routeArr[self::MIDDLEWARE_KEYWORD], self::MIDDLEWARE_BEFORE_RUN);
    }

    private function _afterRunUri($routeArr) {
        $this->_runMiddlewares($routeArr[self::MIDDLEWARE_KEYWORD], self::MIDDLEWARE_AFTER_RUN);
    }

    private function _runMiddlewares(array $middlewares, $action) {
        if (!empty($middlewares)) {
            foreach ($middlewares as $m) {
                try {
                    $this->_runMiddleware($m, $action);
                } catch (Exception $e) {
                    Logger::Error($e->getMessage(), '_runMiddlewares');
                }
            }
        }
    }

    private function _runMiddleware($middleware, $action) {
        $request        = $this->getRequest();
        $middleware     = ucfirst($middleware).self::MIDDLEWARE_SUFFIX;
        $middlewareFile = $this->getMiddlewareDirectory().str_replace('\\', DIRECTORY_SEPARATOR, $middleware).".php";
        $middleware     = self::DEFAULT_MIDDLEWARE_NAMESPACE.$middleware;
        if (!file_exists($middlewareFile)) {
            throw new Dispatcher\Exception("The middleware source:".$middlewareFile." doesn't exist");
        }
        include_once($middlewareFile);
        if (!class_exists($middleware)) {
            throw new Dispatcher\Exception("The middleware class:".$middleware." doesn't exist");
        }
        $mObj = new $middleware();

        if (!method_exists($mObj, $action)) {
            throw new Dispatcher\Exception("Method:".$action." doesn't exist");
        }

        if ($action == self::MIDDLEWARE_BEFORE_RUN) {
            $response        = $this->getResponse();
            $this->_response = $mObj->$action($request, $response);
        } else {
            $response        = $this->getResponse();
            $this->_response = $mObj->$action($request, $response);
        }

        return $this->_response;
    }

    private function _checkRequestMothed($uri, $routeArr) {
        if ('OPTIONS' == $this->getRequest()->getHttpMethod()) {
            return;
        }
        if ($routeArr['method'] != $this->getRequest()->getHttpMethod()) {
            throw new Dispatcher\Exception("The Request:".$uri." must use  ".$routeArr['method']);
        }
    }

    /**
     * load the controller and run the action
     *
     * @return Xly\Mvc\Http\Reponse
     */
    protected function _run($controller, $action) {
        $request  = $this->getRequest();
        $response = $this->getResponse();

        $controllerFile = $this->getControllerDirectory().str_replace('\\', DIRECTORY_SEPARATOR, $controller).".php";
        if (!file_exists($controllerFile)) {
            throw new Dispatcher\Exception("The controller source:".$controllerFile." doesn't exist");
        }

        include_once($controllerFile);

        $controller = self::DEFAULT_CONTROLLER_NAMESPACE.$controller;
        if (!class_exists($controller)) {
            throw new Dispatcher\Exception("The controller class:".$controller." doesn't exist");
        }
        $conObj = new $controller($request, $response);

        return $conObj->__call($action, null);
    }

}// END OF CLASS
