<?php
namespace App\Http\Middleware;

use Xly\Mvc\Http\Request;
use Xly\Mvc\Http\Response;
use Common\ConstAppData;
use Common\XLYException;
use Common\Logger;

class ApiAuthMiddleware
{

    /**
     *
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws CANException
     */
    public function before(Request $request, Response $response)
    {
        $clientIp = $request->getClientIp();
        Logger::Info('request_uri: ' . $request->getRequestURI() . ', request_ip: ' . $clientIp, 'API_AUTH_REQUEST', 'middleware');
        if (in_array($clientIp, config('api_ip_whitelist'))) {
             return $response;
        }
        $params = $request->getParams();
        $this->checkParam($params);
        if(!empty($params['common'])){
            unset($params['common']);
        }
        //密钥获取校验
        $secretKey = ConstAppData::getSecret($params['app_id']);
        if(empty($secretKey)){
            throw new XLYException('Request denied',CANException::ACCESS_TOKEN_ERROR_LOGIN_CODE);
        }
        //时间戳校验
        if(!$this->compareTime($params['timestamp'])){
            throw new XLYException('Request denied',CANException::ACCESS_TOKEN_ERROR_LOGIN_CODE);
        }
        $signature = $params['signature'];
        unset($params['signature']);
        //排序
        ksort($params);
        $query = '';
        foreach ($params as $key => $value) {
            $query .= ($key . '=' . $value . '&');
        }
        //拼装参数
        $query = $this->encodeURI(rtrim($query, '&'));
        //计算密钥值
        $key = strtoupper(md5($query.$secretKey));
        if($key !== $signature){
            throw new XLYException('Request Denied',XLYException::ACCESS_TOKEN_ERROR_LOGIN_CODE);
        }

        return $response;
    }
    private function encodeURI($url) {
        $unescaped = array(
            '%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
            '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
        );
        $reserved = array(
            '%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
            '%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$'
        );
        $score = array(
            '%23'=>'#'
        );
        return strtr(rawurlencode($url), array_merge($reserved,$unescaped,$score));

    }

    /**
     * 公共参数校验
     * @param $params
     * @throws CANException
     */
    private function checkParam($params){
        $needKey = ['signature','timestamp','app_id'];
        foreach($needKey as $vpKey){
            if(!isset($params[$vpKey]) || empty($params[$vpKey])){
                throw new CANException("签名校验失败",CANException::ERROR_CODE_PARAMETER_WRONG);
            }
        }
    }

    /**
     * 时间比较
     * @param $timestamp
     * @return bool
     */
    private function compareTime($timestamp){
        $nowTime = getMillisecond();
        $diffTime = $nowTime-$timestamp;
        if($diffTime > 1000* 60 * 10){
            return false;
        }
        return true;
    }
}
