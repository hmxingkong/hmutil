<?php


namespace hmxingkong\utils;

/**
 * 时间相关工具类
 * Class MTime
 * @package hmxingkong\utils
 */
class MTime
{

    /**
     * 睡眠指定时间（ms）
     * @param $millions
     */
    public static function sleep($millions = 100)
    {
        //1000000 = 1s
        try{ usleep($millions * 1000); }catch(\Exception $e){}
    }

    /**
     * 获取当前时间毫秒值（非0时区）
     * @return float
     */
    public static function getMillions()
    {
        //return intval(microtime(true) * 1000);
        //1490871027.1831
        //1490871027183.1
        return (microtime(true) * 1000);
    }

    /**
     * 获取当前时区
     *      比如东八区，返回 8
     * @return int
     */
    public static function getTimeZone()
    {
        return intval(date('Z') / 3600);
    }

    /**
     * 获取格林威治时间戳 (s)
     * @return int
     */
    public static function getGMTime()
    {
        return (time() - date('Z'));
    }

    /**
     * 将时间字符串转换成格林威治时间戳
     * @param $str
     * @return int
     */
    public static function toGMTTime($str)
    {
        //$timezone = 8;
        $time = intval(strtotime($str));
        if ($time != 0)
            $time = $time - date('Z');
        return $time;
    }

    /**
     * 格式化格林威治时间戳
     * @param $gmtTime
     * @param string $format
     * @return bool|string
     */
    public static function toDate($gmtTime, $format = 'Y-m-d H:i:s')
    {
        if (empty ($gmtTime)) {
            return '';
        }
        $time = $gmtTime + date('Z');
        return date($format, $time);
    }

}