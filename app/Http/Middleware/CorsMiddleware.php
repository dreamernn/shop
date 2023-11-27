<?php
namespace App\Http\Middleware;

use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;

class CorsMiddleware
{
    public function before(Request $request, Response $response)
    {
        $headers = [];
        $origin = $request->getServer('HTTP_HOST');
        $allowOriginArr = explode(',', env('ACCESS_ALLOW_ORIGIN', ''));

        if (
            !empty($allowOriginArr) &&
            (in_array($origin, $allowOriginArr) || env('ACCESS_ALLOW_ORIGIN') == '*')
        ) {
            if (env('ACCESS_ALLOW_ORIGIN') == '*') {
                $origin = '*';
            }
            $headers = [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, mid, Openid, Loctype, Loctoken, Lat, Lng'
            ];
        }
        
        foreach ($headers as $key => $value) {
            header($key . ': ' . $value);
        }

        
        if ('OPTIONS' == $request->getHttpMethod()) {
            $response->output('success');
            exit();
        }
        
        return $response;
        
    }
    
    public function after(Request $request, Response $response)
    {
        return $response;
    }
}
