<?php
namespace Log;
use Psr\Log\LogLevel;

class CommonLog
{
    private static $_logger = null;
    private static $_channel = 'info';  //默认渠道为info
    private static $_cfs = false; //存储在cfs中
    private static $_cfsTurePath = '';

    private static function _getInstance()
    {
        if(self::$_logger == null){
            if (self::$_cfs) {
                self::$_logger = new Logger(self::$_cfsTurePath);
            } else {
                self::$_logger = new Logger(self::$_cfsTurePath);
            }
        }
        return self::$_logger;
    }

    /**
     * 设置日志类参数
     * @param $level
     * @param $message
     * @param $content
     */
    private static function _log($level,$message,$content){
        self::_getInstance()->setLogLevelThreshold($level);
        self::_getInstance()->setLogChannel(self::$_channel);
        self::_getInstance()->setLogPrefix(env('APP_LOG_PREFIX','log-').self::$_channel.'-');
        if (self::$_cfs) {
            self::_getInstance()->setLogFilePath(self::$_cfsTurePath.self::$_channel);
        } else {
            self::_getInstance()->setLogFilePath(self::$_cfsTurePath.self::$_channel);
        }
        self::_getInstance()->setFileHandle('a');
        self::_getInstance()->$level($message,$content);
    }

    /**
     * 渠道设置
     * @param $channel
     * @return CommonLog
     */
    public static function channel($channel, $cfs = false){
        self::$_channel = $channel;
        //self::$_cfs = $cfs;
        self::$_cfs = true;
        self::$_cfsTurePath = env('APP_LOG_BASE_CFS_PATH', '/data/appdata/mirror_api') . '/' . ($_SERVER['SERVER_NAME'] ?? gethostname()) . '/';
        return new CommonLog();
    }

    /**
     * info 日志
     * @param $message
     * @param array $content
     */
    public static function info($message,$content=[]){
        self::_log(LogLevel::INFO,$message,$content);
    }

    /** error 日志
     * @param $message
     * @param array $content
     */
    public static function error($message,$content=[]){
        self::_log(LogLevel::ERROR,$message,$content);
    }

    /**
     * waring 日志
     * @param $message
     * @param array $content
     */
    public static function warning($message,$content=[]){
        self::_log(LogLevel::WARNING,$message,$content);
    }

    /**
     * debug 日志
     * @param $message
     * @param array $content
     */
    public static function debug($message,$content=[]){
        self::_log(LogLevel::DEBUG,$message,$content);
    }
}