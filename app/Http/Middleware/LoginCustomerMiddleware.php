<?php

namespace App\Http\Middleware;

use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;
use Xly\Register;
use Common\XLYException;
use Common\Jwt;

class LoginCustomerMiddleware {
    /**
     * Check Authorization validity
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws XLYException
     */
    public function before(Request $request, Response $response) {
        $authorization = $request->getHeader('Authorization', '');
        if (!$authorization) {
            throw new XLYException(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE, XLYException::USER_NEED_LOGIN_ERROR_CODE);
        }

        $jwt = new Jwt();
        if (!$jwt->isExpire($authorization)) {
            throw new XLYException(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE, XLYException::USER_NEED_LOGIN_ERROR_CODE);
        }

        $userInfo = $jwt->decodeToken($authorization);
        if (empty($userInfo) || $userInfo['user_id'] == 0 || $userInfo['role'] != 0) {
            throw new XLYException(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE, XLYException::USER_NEED_LOGIN_ERROR_CODE);
        }

        Register::set('auth', $userInfo);  //Can be used as temporary storage

        return $response;
    }


    public function after(Request $request, Response $response) {
        return $response;
    }

}
