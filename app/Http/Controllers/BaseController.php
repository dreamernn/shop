<?php
/**
 * @filesource BaseController.php
 * @brief      BaseController
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Http\Controllers;

use Xly\Exception;
use Xly\Mvc\Controller\AbstractController;
use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;
use Common\XLYException;

abstract class BaseController extends AbstractController {
    public function __construct(Request $request, Response $response) {
        parent::__construct($request, $response);
    }

    public function getParams() {
        $params = $this->getRequestParams();

        return $params;
    }

    public function getHeaders() {
        $headers = $this->getRequest()->getHeaders();

        return $headers;
    }

    public function getHeader($key, $default = '') {
        $ret = $this->getRequest()->getHeader($key, $default);

        return $ret;
    }

    public function getParam($key) {
        $params = $this->getRequestParam($key);

        return $params;
    }

    public function getPostParams() {
        return $this->getRequestRawBody();
    }

    protected function validNotEmptyButZero($validParams, $params) {
        foreach ($validParams as $vpKey) {
            if (!isset($params[$vpKey])) {
                throw new XLYException(XLYException::ERROR_MESSAGE_PARAMETER_WRONG, XLYException::ERROR_CODE_PARAMETER_WRONG);
            }
        }
    }

    protected function validMulitIsEmpty($validParams, $params) {
        foreach ($validParams as $vpKey) {
            if (!isset($params[$vpKey]) || empty($params[$vpKey]) || $params[$vpKey] == 'undefined') {
                throw new XLYException(XLYException::ERROR_MESSAGE_PARAMETER_WRONG, XLYException::ERROR_CODE_PARAMETER_WRONG);
            }
        }
    }

    protected function validMulitIsNull($validParams, $params) {
        foreach ($validParams as $vpKey) {
            if (!isset($params[$vpKey]) || ($params[$vpKey] == "")) {
                throw new XLYException(XLYException::ERROR_MESSAGE_PARAMETER_WRONG, XLYException::ERROR_CODE_PARAMETER_WRONG);
            }
        }
    }


    protected function validMulitIsSet($validParams, $params) {
        foreach ($validParams as $vpKey) {
            if (!isset($params[$vpKey])) {
                throw new XLYException(XLYException::ERROR_MESSAGE_PARAMETER_WRONG, XLYException::ERROR_CODE_PARAMETER_WRONG);
            }
        }
    }

    public static function validMulitIsInt($validParams, $params) {
        foreach ($validParams as $vpKey) {
            if (!isset($params[$vpKey]) || empty($params[$vpKey])) {
                throw new XLYException(XLYException::ERROR_MESSAGE_PARAMETER_WRONG, XLYException::ERROR_CODE_PARAMETER_WRONG);
            } else {
                //验证是否数字
                if (!(!is_int($params[$vpKey]) ? (ctype_digit($params[$vpKey])) : true)) {
                    throw new XLYException(XLYException::ERROR_MESSAGE_PARAMETER_WRONG, XLYException::ERROR_CODE_PARAMETER_WRONG);
                }
            }
        }
    }

    /**
     * 校验手机号
     */
    protected function validPhone($phone) {
        //手机号格式不正确
        if (!preg_match('/^1[3456789]\d{9}$/', $phone)) {
            throw new XLYException(XLYException::ERROR_PHONE_FORMAT_MESSAGE, XLYException::ERROR_PHONE_FORMAT_CODE);
        } else {
            return true;
        }
    }

    /**
     * 校验邮箱
     *
     * @param $email
     *
     * @return bool
     * @throws XLYException
     */
    protected function validEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new XLYException(XLYException::ERROR_EMAIL_FORMAT_MESSAGE, XLYException::ERROR_EMAIL_FORMAT_CODE);
        } else {
            return true;
        }
    }

    /**
     * Format response data.
     *
     * @param $message
     *
     * @return array
     */
    protected function responseMsg($message) {
        return $this->responseArray(['message' => $message]);
    }

    /**
     * @param $message
     * @param $statusCode
     *
     * @return mixed
     */
    protected function responseError($message, $statusCode, $data = []) {
        return $this->responseArray(['message' => $message, 'errCode' => $statusCode, 'data' => $data]);
    }

    protected function responseData($data) {
        return $this->responseArray(['data' => $data]);
    }

    /**
     * @param array $array
     *
     * @return mixed
     */
    protected function responseArray(array $array = []) {
        $ret['errCode'] = $array['errCode'] ?? 0;
        $ret['message'] = $array['message'] ?? 'success';
        unset($array['status']);
        unset($array['message']);
        $ret['data'] = $array['data'] ?? [];

        return $ret;
    }

    protected function getUserInfoFormToken() {
        $userLoginInfoArr = getLoginUserInfo();

        $userService = $this->getUserService();
        $userRawData = $userService->getUserByPhone($userLoginInfoArr['account']);
        if (!empty($userRawData)) {
            $userLoginInfoArr['userId']   = $userRawData['user_id'];
            $userLoginInfoArr['userName'] = $userRawData['user_name'];

            $userLoginInfoArr['mallId'] = $userRawData['mall_id'];
        }

        return $userLoginInfoArr;
    }


}
