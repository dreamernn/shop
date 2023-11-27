<?php

namespace Xly\Mvc\Route;

use Xly\Mvc\Http\Request;

abstract class AbstractRoute {

    private $_uri = '';

    //cached params
    private $_params = array();

    /**
    * Get the uri
    *
    * @return string
    */
    public function getUri() {
        return $this->_uri;
    }

    /**
    * Set the uri
    *
    * @param string
    * @return
    */
    public function setUri($uri) {
        $this->_uri = (string) $uri;
        return true;
    }

    /**
    * Get all params
    *
    * @return array
    */
    public function getParams() {
        return $this->_params;
    }

    /**
    * set a variable
    *
    * @param string $key Name of the variable
    * @param string $value Value of the variable
    * @return
    */
    public function setParam($key, $value) {
        $key = (string) $key;
        $this->_params[$key] = $value;

        return;
    }


    /**
    * match the request uri
    *
    * @param Xly\Mvc\Http\Request Http Request Object
    * @return bool
    */
    abstract public function match(Request $request);

}// END OF CLASS
