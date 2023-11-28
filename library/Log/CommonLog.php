<?php
namespace Log;
use Psr\Log\LogLevel;

class CommonLog
{
    private static $_logger = null;
    private static $_channel = 'info';  //channel name
    private static $_nfs = false; //storage in nfs
    private static $_nfsTurePath = '';

    private static function _getInstance()
    {
        if(self::$_logger == null){
            if (self::$_nfs) {
                self::$_logger = new Logger(self::$_nfsTurePath);
            } else {
                self::$_logger = new Logger(self::$_nfsTurePath);
            }
        }
        return self::$_logger;
    }

    /**
     * Set log class parameters
     * @param $level
     * @param $message
     * @param $content
     */
    private static function _log($level,$message,$content){
        self::_getInstance()->setLogLevelThreshold($level);
        self::_getInstance()->setLogChannel(self::$_channel);
        self::_getInstance()->setLogPrefix(env('APP_LOG_PREFIX','log-').self::$_channel.'-');
        if (self::$_nfs) {
            self::_getInstance()->setLogFilePath(self::$_nfsTurePath.self::$_channel);
        } else {
            self::_getInstance()->setLogFilePath(self::$_nfsTurePath.self::$_channel);
        }
        self::_getInstance()->setFileHandle('a');
        self::_getInstance()->$level($message,$content);
    }

    /**
     * Set channel
     * @param $channel
     * @return CommonLog
     */
    public static function channel($channel, $nfs = false){
        self::$_channel = $channel;
        self::$_nfs = true;
        self::$_nfsTurePath = env('APP_LOG_BASE_PATH', '/data/appdata/nfs') . '/' . ($_SERVER['SERVER_NAME'] ?? gethostname()) . '/';
        return new CommonLog();
    }

    /**
     * info
     * @param $message
     * @param array $content
     */
    public static function info($message,$content=[]){
        self::_log(LogLevel::INFO,$message,$content);
    }

    /** error
     * @param $message
     * @param array $content
     */
    public static function error($message,$content=[]){
        self::_log(LogLevel::ERROR,$message,$content);
    }

    /**
     * waring
     * @param $message
     * @param array $content
     */
    public static function warning($message,$content=[]){
        self::_log(LogLevel::WARNING,$message,$content);
    }

    /**
     * debug
     * @param $message
     * @param array $content
     */
    public static function debug($message,$content=[]){
        self::_log(LogLevel::DEBUG,$message,$content);
    }
}