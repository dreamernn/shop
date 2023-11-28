<?php

use Common\Jwt;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Customer\OrderController;
use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;
use Xly\Register;
use Common\XLYException;

require dirname(__DIR__).'/bootstrap/app.php';
include_once 'mock/mock_data.php';

class ProductTest extends TestCase {
    /**
     * unitTest for customer checkout
     *
     * @throws \Common\JwtException
     */
    public function testCustomerCheckOut() {
        global $mock;
        self::setAuth();
        $request   = new Request();
        $response  = new Response();
        $order     = new OrderController($request, $response);
        $orderData = $mock['customer']['checkout'];
        $request->setRawBody(json_encode($orderData, JSON_UNESCAPED_UNICODE));
        $result = $order->add();
        $this->assertIsArray($result);
        $this->assertTrue(
            array_key_exists('errCode', $result) && in_array($result['errCode'], [200])
        );
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
    }

    /**
     * set Auth
     *
     * @param int $userType
     *
     * @throws \Common\JwtException
     */
    private static function setAuth(int $userType = 0) {   // 0 Customer 1 Admin
        global $mock;
        $authorization = 0 == $userType ? $mock['customer_authorization'] : $mock['admin_authorization'];
        $jwt           = new Jwt();
        $userInfo      = $jwt->decodeToken($authorization);
        if (empty($userInfo)) {
            exit(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE);
        }
        Register::set('auth', $userInfo);  //Can be used as temporary storage
    }
}
