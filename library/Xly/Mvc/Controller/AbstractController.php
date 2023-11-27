<?php

namespace Xly\Mvc\Controller;

use Xly\Mvc\Http\Common;
use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;

abstract class AbstractController {

    private $_request = null;

    private $_response = null;


    /**
     * AbstractController constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response) {
        $this->_request = $request;
        $this->_response = $response;
    }

    /**
    * callback function before action is executed
    */
    public function init() {
    }

    /**
    * callback function after action has been executed
    */
    public function shutdown() {
    }

    /**
     * @return Request|null
     */
    public function getRequest() {
        return $this->_request;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getRequestParam($key) {
        return $this->_request->getParam($key);
    }

    /**
     * @return array
     */
    public function getRequestParams() {
        return $this->_request->getParams();
    }

    /**
     * @return array
     */
    public function getValidRequestParams() {
        return $this->_request->getParams();
    }


    /**
     * @return string
     */
    public function getRequestRawBody() {
        return $this->_request->getRawBody();
    }


    /**
     * @return Response|null
     */
    public function getResponse() {
        return $this->_response;
    }

    /**
     * @param $method
     * @param $args
     * @return Response|null
     */
    public function __call($method, $args) {
        $response = $this->getResponse();

        try {
            $this->init();

            if (!method_exists($this, $method)) {
                throw new Exception("Method: ".$method." doesn't exist", Common::SC_NOT_FOUND);
            }

            $result = $this->$method();
            $response->setReturn($result);
        } catch (Exception $e) {
            $response->setException($e);   
        }

        $this->shutdown();
 
        return $response;
    }


    /**
     * 同步返回
     * @param int $code
     * @param string $message
     * @param array $data
     */
    public function response_json($data = array(), $code = 200, $message = "success"){
        header('Content-Type: application/json');
        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );
        \Common\Logger::Info($result,"response_json:");
        echo json_encode($result);
        exit;
    }

    /**
     * 异步返回
     * @param int $code
     * @param string $message
     * @param array $data
     */
    public function async_response_json($data =array(), $code = 200, $message = "success"){
        header('Content-Type: application/json');
        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );
        echo json_encode($result);
        fastcgi_finish_request();
    }
    

}//END OF CLASS
