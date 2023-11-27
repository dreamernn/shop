<?php
namespace Log;
use DateTime;
use Psr\Log\AbstractLogger;
use RuntimeException;
use Psr\Log\LogLevel;
/**
 * Finally, a light, permissions-checking logging class.
 *
 * Originally written for use with wpSearch
 *
 * Usage:
 * $log = new Katzgrau\KLogger\Logger('/var/log/', Psr\Log\LogLevel::INFO);
 * $log->info('Returned a million search results'); //Prints to the log file
 * $log->error('Oh dear.'); //Prints to the log file
 * $log->debug('x = 5'); //Prints nothing due to current severity threshhold
 *
 * @author  Kenny Katzgrau <katzgrau@gmail.com>
 * @since   July 26, 2008
 * @link    https://github.com/katzgrau/KLogger
 * @version 1.0.0
 */
/**
 * Class documentation
 */
class Logger extends AbstractLogger
{
    /**
     * KLogger options
     *  Anything options not considered 'core' to the logging library should be
     *  settable view the third parameter in the constructor
     *
     *  Core options include the log file path and the log threshold
     *
     * @var array
     */
    protected $options = array (
        'extension'      => 'log',
        'dateFormat'     => 'Y-m-d G:i:s.u',
        'filename'       => false,
        'flushFrequency' => false,
        'prefix'         => 'log_',
        'logFormat'      => false,
        'appendContext'  => false,
    );
    /**
     * Path to the log file
     * @var string
     */
    private $logFilePath;
    /**
     * Current minimum logging threshold
     * @var integer
     */
    protected $logLevelThreshold = LogLevel::DEBUG;
    /**
     * The number of lines logged in this instance's lifetime
     * @var int
     */
    private $logLineCount = 0;
    /**
     * Log Levels
     * @var array
     */
    protected $logLevels = array(
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT     => 1,
        LogLevel::CRITICAL  => 2,
        LogLevel::ERROR     => 3,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 5,
        LogLevel::INFO      => 6,
        LogLevel::DEBUG     => 7
    );
    /**
     * This holds the file handle for this instance's log file
     * @var resource
     */
    private $fileHandle;
    /**
     * This holds the last line logged to the logger
     *  Used for unit tests
     * @var string
     */
    private $lastLine = '';
    /**
     * Octal notation for default permissions of the log file
     * @var integer
     */
    private $defaultPermissions = 0777;
    /**
     * Log Type
     * @var string
     */
    private $logChannel = 'access';
    /**
     * Class constructor
     *
     * @param string $logDirectory      File path to the logging directory
     * @param string $logLevelThreshold The LogLevel Threshold
     * @param array  $options
     *
     * @internal param string $logFilePrefix The prefix for the log file name
     * @internal param string $logFileExt The extension for the log file
     */
    public function __construct($logDirectory, $logLevelThreshold = LogLevel::DEBUG, array $options = array())
    {
        $this->logLevelThreshold = $logLevelThreshold;
        $this->options = array_merge($this->options, $options);
        $logDirectory = rtrim($logDirectory, DIRECTORY_SEPARATOR);
        if ( ! file_exists($logDirectory)) {
            mkdir($logDirectory, $this->defaultPermissions, true);
        }
    }
    /**
     * @param string $stdOutPath
     */
    public function setLogToStdOut($stdOutPath) {
        $this->logFilePath = $stdOutPath;
    }
    /**
     * @param string $logDirectory
     */
    public function setLogFilePath($logDirectory) {
        $logDirectory = rtrim($logDirectory, DIRECTORY_SEPARATOR);
        if ( ! file_exists($logDirectory)) {
            mkdir($logDirectory, $this->defaultPermissions, true);
        }
        $dateFormat = $this->logChannel == 'access' ? date('Y-m-d-H'): date('Y-m-d');
        $this->logFilePath = $logDirectory.DIRECTORY_SEPARATOR.$this->options['prefix'].$dateFormat.'.'.$this->options['extension'];
    }
    /**
     * @param $writeMode
     *
     * @internal param resource $fileHandle
     */
    public function setFileHandle($writeMode) {
        $this->fileHandle = fopen($this->logFilePath, $writeMode);
        if ( ! $this->fileHandle) {
            throw new RuntimeException('The file could not be opened. Check permissions.');
        }
        chmod($this->logFilePath, 0777);
    }
    /**
     * Class destructor
     */
    public function __destruct()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }
    /**
     * Sets the date format used by all instances of KLogger
     *
     * @param string $dateFormat Valid format string for date()
     */
    public function setDateFormat($dateFormat)
    {
        $this->options['dateFormat'] = $dateFormat;
    }
    /**
     * Sets the Log Level Threshold
     *
     * @param string $logLevelThreshold The log level threshold
     */
    public function setLogLevelThreshold($logLevelThreshold)
    {
        $this->logLevelThreshold = $logLevelThreshold;
    }

    /**
     * Sets the Log prefix
     * @param $prefix
     */
    public function setLogPrefix($prefix){
        $this->options['prefix'] = $prefix;
    }

    /**
     * Sets the Log type
     * @param $channel
     */
    public function setLogChannel($channel){
        $this->logChannel = $channel;
    }
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->logLevels[$this->logLevelThreshold] < $this->logLevels[$level]) {
            return;
        }
        $message = $this->formatMessage($level, $message, $context);
        $this->write($message);
    }
    /**
     * Writes a line to the log without prepending a status or timestamp
     *
     * @param string $message Line to write to the log
     * @return void
     */
    public function write($message)
    {
        if (null !== $this->fileHandle) {
            if (fwrite($this->fileHandle, $message) === false) {
                throw new RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
            } else {
                $this->lastLine = trim($message);
                $this->logLineCount++;
                if ($this->options['flushFrequency'] && $this->logLineCount % $this->options['flushFrequency'] === 0) {
                    fflush($this->fileHandle);
                }
            }
        }
    }
    /**
     * Get the file path that the log is currently writing to
     *
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->logFilePath;
    }
    /**
     * Get the last line logged to the log file
     *
     * @return string
     */
    public function getLastLogLine()
    {
        return $this->lastLine;
    }
    /**
     * Formats the message for logging.
     *
     * @param  string $level   The Log Level of the message
     * @param  string $message The message to log
     * @param  array  $context The context
     * @return string
     */
    protected function formatMessage($level, $message, $context)
    {
        if($this->logChannel == 'access') {
            $parts = array(
                'date' => $this->getTimestamp(),
                'level' => strtoupper($level),
                'priority' => $this->logLevels[$level],
                'pname' => $message,
                'action' => $context['action'],
                'method' => $context['request-method'],
                'trace_id' => $context['trace_id'] ?? '',
                'ip' => $context['request-ip'],
                'headers' => $context['request-headers'],
                'authorization' => $context['authorization'],
                'request' => $context['request-content'],
                'response' => $context['response-content'],
                'time-spent' => $context['time-spent'],
                'slow' => $context['slow-request'],
            );
            $message = json_encode($parts,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }else{
            $info =  "[" . date('Y-m-d H:i:s') ."] [".CAN_LOG_TOKEN."] ";
            $info .= $this->logChannel . " [".$level."] " . $message;
            if(!empty($context)){
                $info .= ' context:'.json_encode($context,JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
            }
            $message = $info;
        }
        return $message.PHP_EOL;
    }
    /**
     * Gets the correctly formatted Date/Time for the log entry.
     *
     * PHP DateTime is dump, and you have to resort to trickery to get microseconds
     * to work correctly, so here it is.
     *
     * @return string
     */
    private function getTimestamp()
    {
        $originalTime = microtime(true);
        $micro = sprintf("%06d", ($originalTime - floor($originalTime)) * 1000000);
        $date = new DateTime(date('Y-m-d H:i:s.'.$micro, $originalTime));
        return $date->format($this->options['dateFormat']);
    }
    /**
     * Takes the given context and coverts it to a string.
     *
     * @param  array $context The Context
     * @return string
     */
    protected function contextToString($context)
    {
        $export = '';
        foreach ($context as $key => $value) {
            $export .= "{$key}: ";
            $export .= preg_replace(array(
                '/=>\s+([a-zA-Z])/im',
                '/array\(\s+\)/im',
                '/^  |\G  /m'
            ), array(
                '=> $1',
                'array()',
                '    '
            ), str_replace('array (', 'array(', var_export($value, true)));
            $export .= PHP_EOL;
        }
        return str_replace(array('\\\\', '\\\''), array('\\', '\''), rtrim($export));
    }
    /**
     * Indents the given string with the given indent.
     *
     * @param  string $string The string to indent
     * @param  string $indent What to use as the indent.
     * @return string
     */
    protected function indent($string, $indent = '    ')
    {
        return $indent.str_replace("\n", "\n".$indent, $string);
    }
}
