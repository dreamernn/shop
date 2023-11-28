<?php

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\IndexController;
use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;

require dirname(__DIR__).'/bootstrap/app.php';
include_once 'mock/mock_data.php';

class IndexTest extends TestCase {
    public function testIndex() {
        global $mock;
        $request       = new Request();
        $response      = new Response();
        $indexObj      = new IndexController($request, $response);
        $authorization = $mock['admin_authorization'];
        $request->setHeader('Authorization', $authorization);
        $result = $indexObj->index();
        $this->assertTrue(
            array_key_exists('errCode', $result) && in_array($result['errCode'], [2001, 2002])
        );
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
    }
}
