<?php
$router = new \Xly\RouterRegister();
/*$router->get('/', 'IndexController@index');*/

$router->group(['prefix' => ''], function () use ($router) {
    $router->get('/', 'IndexController@index');
});

$router->group(['prefix' => 'user', 'middleware' => []], function () use ($router) {
    $router->get('login', 'UserController@index');
    $router->post('doLogin', 'UserController@doLogin');
    $router->post('doRegister', 'UserController@doRegister');
});

$router->group(['prefix'=>'customer', 'middleware' => ['cors','LoginCustomer']], function () use ($router) {
    $router->get('product-list', 'Customer\ProductController@list');
    $router->get('cart-list', 'Customer\CartController@list');
    $router->post('cart_add', 'Customer\CartController@add');
    $router->get('cart-total-price', 'Customer\CartController@getTotalAndPrice');
//    $router->post('cart_remove', 'Customer\CartController@remove');
    $router->put('order_add', 'Customer\OrderController@add');
});

$router->group(['prefix'=>'admin', 'middleware' => ['LoginAdmin']], function () use ($router) {
    $router->get('product-list', 'Admin\ProductController@list');
    $router->post('product-set', 'Admin\ProductController@edit');
    $router->get('cart-list', 'Admin\CartController@list');
    $router->get('cart-info', 'Admin\CartController@info');
});