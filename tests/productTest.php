<?php

use Common\Jwt;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;
use Xly\Register;
use Common\XLYException;

require dirname(__DIR__).'/bootstrap/app.php';
include_once 'mock/mock_data.php';

class ProductTest extends TestCase {
    /**
     * unitTest for customer list product
     *
     * @throws \Common\JwtException
     */
    public function testCustomerProductList() {
        self::setAuth();
        $request  = new Request();
        $response = new Response();
        $product  = new CustomerProductController($request, $response);
        $result   = $product->list();
        $this->assertIsArray($result);
        $this->assertTrue(
            array_key_exists('errCode', $result) && in_array($result['errCode'], [200, 2001, 2002])
        );
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
    }

    /**
     * unitTest for admin list product
     *
     * @throws \Common\JwtException
     */
    public function testAdminProductList() {
        self::setAuth(1);
        $request  = new Request();
        $response = new Response();
        $product  = new AdminProductController($request, $response);
        $result   = $product->list();
        $this->assertIsArray($result);
        $this->assertTrue(
            array_key_exists('errCode', $result) && in_array($result['errCode'], [200, 2001, 2002])
        );
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
    }

    /**
     * unitTest for admin list product
     *
     * @throws \Common\JwtException
     */
    public function testAdminEditProduct() {
        global $mock;
        self::setAuth(1);
        $request  = new Request();
        $response = new Response();
        $product  = new AdminProductController($request, $response);
        $editData = $mock['admin']['edit_product'];
        foreach ($editData as $k => $v) {
            if ($k == 'name') {
                $v = randWord(10);
            }
            $request->setParam($k, $v);
        }

        $result = $product->edit();
        $this->assertIsArray($result);
        $this->assertTrue(
            array_key_exists('errCode', $result) && in_array($result['errCode'], [200, 2001, 2002])
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
