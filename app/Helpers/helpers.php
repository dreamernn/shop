<?php

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     *
     * @return string
     */
    function base_path($path = '') {
        $basePath = dirname(__DIR__).'/../';

        return $basePath.$path;
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function env($key, $default = null) {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        return $value;
    }
}

if (!function_exists('curlRequest')) {
    /**
     * send curl request
     *
     * @param        $url
     * @param string $method [get or post]
     * @param array  $data
     * @param array  $jsonFormat
     * @param array  $header header extension
     *
     * @return array|mixed
     */
    function curlRequest($url, $data = null, $method = 'post', $jsonFormat = true, $header = []) {
        //request
        $requestCounts = 0;
        do {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//avoid verify of ssl
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            if ($method == 'post' && !empty($data)) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            $header = empty($header) ? ['Content-Type:application/json'] : $header;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $result  = curl_exec($ch);
            $error[] = curl_error($ch);
            curl_close($ch);
            //failure
        } while (($requestCounts++) < 3 && $result === false);

        if (false !== $result) {
            if (!$jsonFormat) {
                return $result;
            }
            $responseData = json_decode($result, true);

            return $responseData;
        }

        return [];
    }
}

if (!function_exists('responseSuccess')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $msg
     * @param mixed  $data
     * @param int    $code
     *
     * @return mixed
     */
    function responseSuccess($msg, $data = [], $code = 200) {
        $msg = empty($msg) ? 'success' : $msg;
        $ret = [
            'errCode' => $code,
            'message' => $msg,
            'data'    => $data,
        ];

        return $ret;
    }
}

if (!function_exists('responseFail')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $msg
     * @param int    $code
     *
     * @return mixed
     */
    function responseFail($msg, $code = 410, $data = []) {
        $msg = empty($msg) ? 'fail' : $msg;
        $ret = [
            'errCode' => $code,
            'message' => $msg,
            'data'    => $data,
        ];

        return $ret;
    }
}

if (!function_exists('randomCode')) {
    /**
     * generate random numbers
     *
     * @param integer $len number lenght
     *
     * @return string
     */
    function randomCode($length) {
        $code    = '';
        $pattern = '1234567890';
        for ($i = 0; $i < $length; ++$i) {
            $code .= $pattern[mt_rand(0, 9)];    // make rand number
        }

        return $code;
    }
}


if (!function_exists('randWord')) {
    /**
     * A random string of numbers and letters
     *
     * @param int $n
     *
     * @return bool|string
     */
    function randWord($n = 8) {
        return $n < 1 ? '' : substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ($n + 3) / 4)), 0, $n);
    }
}

if (!function_exists('sendResponse')) {
    function sendResponse($result) {
        if (is_null($result)) {
            return;
        }
        header('Content-Type: application/json');
        if (isOnline() && is_array($result)) {
            unset($result['debug']);
        }
        echo json_encode($result);
        fastcgi_finish_request();
    }
}

if (!function_exists('isOnline')) {
    /**
     * if online env
     *
     * @return bool
     */
    function isOnline() {
        return getenv('APP_ENV') == 'online' || getenv('APP_ENV') == 'online_stg' || getenv('APP_ENV') == 'stg' ? true : false;
    }
}

if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string $key
     * @param mixed        $default
     *
     * @return mixed
     */
    function config($key = null, $default = null) {
        if (is_null($key)) {
            return \Xly\Register::get('config');
        }
        if (is_array($key)) {
            return \Xly\Register::set('config', $key);
        }

        return \Xly\Register::get('config')[$key];
    }
}