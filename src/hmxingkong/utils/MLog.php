<?php


namespace hmxingkong\utils;

/**
 * 日志类
 * Class MLog
 * @package hmxingkong\alarm
 */
class MLog
{
    private $enableLog = false;

    private $tag = '';

    private $logDir = '';

    private $logFileName = '';

    const LEVEL_INFO = 'INFO';
    const LEVEL_WARN = 'WARN';
    const LEVEL_ERROR = 'ERROR';

    /**
     * MLog constructor.
     * @param string $logDir
     * @param string $logFileName
     * @param string $tag
     * @param bool $enableLog
     */
    public function __construct($logDir, $logFileName, $tag, $enableLog)
    {
        $logFileName = empty($logFileName) ? 'default.log' : $logFileName;

        if(!MString::endWith($logDir, '/')){
            $logDir .= '/';
        }

        $this->logDir = $logDir;
        $this->logFileName = $logFileName;
        $this->tag = $tag;
        $this->enableLog = $enableLog;
    }

    /**
     * 记录日志
     * @param $logMsg
     * @param string $level
     * @return bool
     */
    public function log($logMsg, $level=MLog::LEVEL_INFO)
    {
        if(!$this->enableLog){
            return false;
        }

        if(empty($this->logDir)){
            return false;  //TODO defaultVal ?
        }

        $logDir = $this->logDir . date('Y-m-d') . '/';
        if(!MDir::mkdir($logDir)){
            return false;
        }

        $logPrefix = '[' . date('Y-m-d H:i:s') . ']' . ( empty($this->tag) ? '' : '[' . $this->tag . ']' ) . '[' . $level . ']';
        @file_put_contents($logDir . $this->logFileName, $logPrefix . ' ' . $logMsg . PHP_EOL, FILE_APPEND);
    }

}