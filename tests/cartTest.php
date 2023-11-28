<?php

use Common\Jwt;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Customer\CartController as CustomerCartController;
use App\Http\Controllers\Admin\CartController as AdminCartController;
use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;
use Xly\Register;
use Common\XLYException;

require dirname(__DIR__).'/bootstrap/app.php';
include_once 'mock/mock_data.php';

class CartTest extends TestCase {
    /**
     * unitTest for customer add cart
     *
     * @throws \Common\JwtException
     */
    public function testCustomerAddCart() {
        global $mock;
        self::setAuth();
        $request  = new Request();
        $response = new Response();
        $cart     = new CustomerCartController($request, $response);
        $editData = $mock['customer']['add_cart'];
        foreach ($editData as $k => $v) {
            if ($k == 'quantity') {
                $v = randomCode(2);
            }
            $request->setParam($k, $v);
        }


        $result = $cart->add();
        $this->assertIsArray($result);
        $this->assertTrue(
            array_key_exists('errCode', $result) && in_array($result['errCode'], [200, 2001, 2002])
        );
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
    }

    /**
     * unitTest for admin cart list
     *
     * @throws \Common\JwtException
     */
    public function testAdminCartList() {
        self::setAuth(1);
        $request  = new Request();
        $response = new Response();
        $cart     = new AdminCartController($request, $response);
        $result   = $cart->list();
        $this->assertIsArray($result);
        $this->assertTrue(
            array_key_exists('errCode', $result) && in_array($result['errCode'], [200, 2001, 2002])
        );
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
        $this->assertEquals('success!', $result['message']);
    }

    /**
     * unitTest for admin cart info
     *
     * @throws \Common\JwtException
     */
    public function testAdminCartInfo() {
        self::setAuth(1);
        $request  = new Request();
        $response = new Response();
        $cart     = new AdminCartController($request, $response);
        $request->setParam('cart_id', 6);
        $result = $cart->info();
        $this->assertIsArray($result);
        $this->assertTrue(
            array_key_exists('errCode', $result) && in_array($result['errCode'], [200])
        );
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
        $this->assertEquals('success!', $result['message']);
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
