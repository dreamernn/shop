<?php
/**
 * @filesource RouteRegister.php
 * @brief      Registering routes and middleware.
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace Xly;

class RouterRegister {

    static private $_router = [];
    static private $_paramsRouter = [];
    private $_prefix = '';
    private $_middleware = [];
    private $_overallMiddleware = [];

    public function overall(array $middleware) {
        $this->_overallMiddleware = $middleware['middleware'];

        return $this;
    }

    public function group(array $attributes, \Closure $callback) {
        $this->_prefix     = $attributes['prefix'].DIRECTORY_SEPARATOR;
        $this->_middleware = isset($attributes['middleware']) ? $attributes['middleware'] : [];
        call_user_func($callback, $this);
        $this->_prefix = '';
    }

    public function get($uri, $action) {
        $this->_method($uri, $action, 'GET');
    }

    public function post($uri, $action) {
        $this->_method($uri, $action, 'POST');
    }

    public function put($uri, $action) {
        $this->_method($uri, $action, 'PUT');
    }

    public function delete($uri, $action) {
        $this->_method($uri, $action, 'DELETE');
    }

    private function _method($uri, $action, $method = 'GET') {
        if (!empty($this->_overallMiddleware)) {
            $this->_middleware = array_merge($this->_overallMiddleware, $this->_middleware);
            $this->_middleware = array_unique($this->_middleware);
        }

        $uri = strtolower(trim($this->_prefix.$uri, "/") ?: '/');
        // Path parameters are not allowed to be placed at the beginning of the route.
        if (strpos($uri, '{')) {
            preg_match_all('/\{(.*?)\}/', $uri, $matches);
            $uri                       = trim(preg_replace('/\{(.*?)\}/', '', $uri), '/');
            $pathParams                = $matches[1];
            self::$_paramsRouter[$uri] = ['method' => $method, 'action' => $action, 'middleware' => $this->_middleware, 'route_params' => $pathParams];
        } else {
            self::$_router[$uri] = ['method' => $method, 'action' => $action, 'middleware' => $this->_middleware];
        }
    }

    static public function getRouter($name, $default = null) {
        // Match Regular Routes
        if (isset(self::$_router[$name])) {
            return self::$_router[$name];
        }

        // Match Regular Routes; If No Match, Match Path Parameter Routes
        foreach (self::$_paramsRouter as $uri => $route) {
            // 匹配路由前缀
            if (strpos($name, $uri) !== false && !empty($route['route_params'])) {
                // 组装路由路径参数
                $routeParamsValue = explode('/', trim(str_replace($uri, '', $name), '/'));
                $routeParams      = [];
                foreach ($route['route_params'] as $key => $routeParamsKey) {
                    $routeParams[$routeParamsKey] = $routeParamsValue[$key];
                }
                $route['route_params'] = $routeParams;

                return $route;
            }
        }

        return $default;
    }
}
