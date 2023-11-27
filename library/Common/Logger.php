<?php
/**
 * @filesource Logger.php
 * @brief      日志
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2022-08-09
 */

namespace Common;

class Logger {

    const LOG_LEVEL_ERROR = 1;
    const LOG_LEVEL_WARN  = 2;
    const LOG_LEVEL_INFO  = 3;
    const LOG_LEVEL_DEBUG = 4;

    private static $logLevelArr = [
        1 => 'ERROR',
        2 => 'WARN',
        3 => 'INFO',
        4 => 'DEBUG',
    ];

    private static function _log($level = self::LOG_LEVEL_ERROR, $msg = '', $key = '', $file = '') {
        ini_set('date.timezone', 'Asia/Shanghai');
        $logFile  = config('log_path');
        $cfgLevel = config('log_level');
        if ($cfgLevel >= $level) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $pid       = posix_getpid();
            $filenames = explode('/', $backtrace[1]['file']);
            $filename  = $filenames[count($filenames) - 1];
            $log       = "[".date("Y-m-d H:i:s")."] [".self::$logLevelArr[$level]."] [".CAN_LOG_TOKEN
                         ."] [$key] pid: $pid, file: {$filename}, line: {$backtrace[1]['line']}, ".json_encode($msg, JSON_UNESCAPED_UNICODE)."\n";
            if (empty($file)) {
                $file = sprintf($logFile, $_SERVER['SERVER_NAME'] ?? gethostname(), date("Ymd"));
            } else {
                $file = sprintf($logFile, $_SERVER['SERVER_NAME'] ?? gethostname(), $file."_".date("Ymd"));
            }
            $pathInfo = pathinfo($file);
            if (!file_exists($file)) {
                mkdir($pathInfo['dirname'], 0777, true);
            }
            //$old = umask(0111);
            file_put_contents($file, $log, FILE_APPEND);
            //umask($old);
        }
    }

    public static function Error($msg, $key, $file = '') {
        self::_log(self::LOG_LEVEL_ERROR, $msg, $key, $file);
    }

    public static function Warn($msg, $key, $file = '') {
        self::_log(self::LOG_LEVEL_WARN, $msg, $key, $file);
    }

    public static function Info($msg, $key, $file = '') {
        self::_log(self::LOG_LEVEL_INFO, $msg, $key, $file);
    }

    public static function Debug($msg, $key, $file = '') {
        self::_log(self::LOG_LEVEL_DEBUG, $msg, $key, $file);
    }
}
