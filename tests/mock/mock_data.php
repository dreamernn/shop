<?php
return $mock = [
    'customer_authorization' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsb2NhbC5zaG9wX2FwaS5jb20iLCJhdWQiOiJzaG9wIiwianRpIjoic2hvcDBqMWYyYTNjIiwiaWF0IjoxNzAwODc3NzAyLCJuYmYiOjE3MDA4Nzc3MDIsImV4cCI6MTcwMzQ2OTcwMiwicmFuZE51bSI6MTU0NzczMCwidXNlcl9pZCI6IjEiLCJ1c2VybmFtZSI6InVzZXIxIiwicGFzc3dvcmQiOiJlMTBhZGMzOTQ5YmE1OWFiYmU1NmUwNTdmMjBmODgzZSIsInJvbGUiOiIwIn0.4BMIaGMYx_IkpdUbqfK8hVBoYJ_ZOCFMIHCy76A4Dhg',
    'admin_authorization'    => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsb2NhbC5zaG9wX2FwaS5jb20iLCJhdWQiOiJzaG9wIiwianRpIjoic2hvcDBqMWYyYTNjIiwiaWF0IjoxNzAwODg0NDE3LCJuYmYiOjE3MDA4ODQ0MTcsImV4cCI6MTcwMzQ3NjQxNywicmFuZE51bSI6ODkyMDk4NiwidXNlcl9pZCI6IjIiLCJ1c2VybmFtZSI6ImFkbWluMSIsInBhc3N3b3JkIjoiZTEwYWRjMzk0OWJhNTlhYmJlNTZlMDU3ZjIwZjg4M2UiLCJyb2xlIjoiMSJ9.r0pvrfMHTH9n8qdycl2W3J9dRy_YNXhHL4YwmqLuAqQ
',
    'admin'                  => [
        'edit_product' => [
            'product_id'  => 1,
            'sku'         => 'XL',
            'name'        => '',
            'description' => 'test description',
            'price'       => 100.00,
        ],
    ],
    'customer'               => [
        'add_cart' => [
            'product_id' => 1,
            'quantity'   => 10,
            'sku'        => 'XL',
        ],
        'checkout' => [
            'first_name' => 'Damon',
            'last_name'  => 'Meng',
            'email' => 'xiangchen0814@gmail.com',
            'cart_list' => '[]',

        ]
    ],
];