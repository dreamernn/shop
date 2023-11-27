<?php

namespace App\Http\Middleware;

use Xly\Mvc\Http\Request;
use Feishu\CustomBotV2;
use Log\CommonLog;

class LogMiddleware {
    public function log($ret) {
        $allow_get_urls       = [''];
        $allow_router_prefixs = ['user', 'customer', 'admin',];

        $request       = new Request();
        $req_url       = $request->getRequestURI();
        $router_prefix = strstr(ltrim($req_url, '/'), '/', true);

        if (
            in_array($req_url, $allow_get_urls) || in_array($router_prefix, $allow_router_prefixs)
            || ($request->getHttpMethod() != 'GET'
                && $request->getHttpMethod() != 'OPTIONS')) {
            $time_spent = round((microtime(true) - APP_START) * 1000);
            $content    = [
                'request-method'   => $request->getHttpMethod(),
                'request-content'  => $request->getParams() ?: json_decode($request->getRawBody(), true),
                'request-ip'       => $request->getClientIp(),
                'request-headers'  => $request->getHeaders(),
                'authorization'    => $request->getHeader('Authorization'),
                'response-content' => $ret,
                'action'           => $request->getRequestURI(),
                'time-spent'       => $time_spent,
                'slow-request'     => $time_spent > 1000 ? 1 : 0,
                'trace_id'         => CAN_LOG_TOKEN,
            ];
            CommonLog::channel('access')->info('magic-api', $content);
        }
    }

    public function error($ret) {
        $request    = new Request();
        $time_spent = round((microtime(true) - APP_START) * 1000);
        $content    = [
            'request-method'   => $request->getHttpMethod(),
            'request-content'  => $request->getParams(),
            'request-ip'       => $request->getClientIp(),
            'request-headers'  => $request->getHeaders(),
            'authorization'    => $request->getHeader('Authorization'),
            'response-content' => $ret,
            'userLoginInfo'    => getLoginUserInfo(),
            'action'           => $request->getRequestURI(),
            'time-spent'       => 0,
            'slow-request'     => $time_spent > 1000 ? 1 : 0,
            'trace_id'         => CAN_LOG_TOKEN,
            'debug'            => $ret['debug'] ?? '',
            'traceString'      => $ret['traceString'] ?? '',
        ];
        if (!empty($content['traceString']) && !empty($ret['traceString'])) {
            unset($content['response-content']['traceString']);
        }
        CommonLog::channel('access')->error('magic-api', $content);
        $feiShuToken = getenv('FEISHU_CUSTOM_BOT_OF_ERROR');
        if (!empty($feiShuToken)) {
            (new CustomBotV2($feiShuToken))->sendArray('ERROR '.$request->getRequestURI().' '.$ret['message'], $content);
        }
    }

    public function warning($ret) {
        $request    = new Request();
        $time_spent = round((microtime(true) - APP_START) * 1000);
        $content    = [
            'request-method'   => $request->getHttpMethod(),
            'request-content'  => $request->getParams(),
            'request-ip'       => $request->getClientIp(),
            'authorization'    => $request->getHeader('Authorization'),
            'response-content' => $ret,
            'userLoginInfo'    => getLoginUserInfo(),
            'action'           => $request->getRequestURI(),
            'time-spent'       => 0,
            'slow-request'     => $time_spent > 1000 ? 1 : 0,
            'trace_id'         => CAN_LOG_TOKEN,
        ];
        CommonLog::channel('access')->warning('magic-api', $content);
        $feiShuToken = getenv('FEISHU_CUSTOM_BOT_OF_WARNING');
        if (!empty($feiShuToken)) {
            (new CustomBotV2($feiShuToken))->sendArray('WARNING '.$request->getRequestURI().' '.$ret['message'], $content);
        }
    }
}
