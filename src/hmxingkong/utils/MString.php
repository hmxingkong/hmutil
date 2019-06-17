<?php


namespace hmxingkong\utils;

/**
 * 字符串相关工具类
 * Class MString
 * @package hmxingkong\utils
 */
class MString
{

    /**
     * 判断是否以指定字符串开头
     * @param $str : 原字符串
     * @param $pattern : 查找对象
     * @return bool
     */
    public static function startWith($str, $pattern)
    {
        return (strpos($str, $pattern) === 0);
    }

    /**
     * 判断是否以指定字符串结尾
     * @param $str : 原字符串
     * @param $pattern : 查找对象
     * @return bool
     */
    public static function endWith($str, $pattern)
    {
        //BUG 作用对象为字符，非字符串
        //return (strrchr($str, $pattern) === $pattern);
        //substr($file, strrpos($str, $pattern)) === $pattern

        $len = strlen($pattern);
        if($len == 0) return true;
        return substr($str, -$len) === $pattern;
    }

    /**
     * 判断是否包含
     * @param $str : 原字符串
     * @param $pattern : 查找对象
     * @return bool
     */
    public static function contains($str, $pattern)
    {
        return strpos($str, $pattern) !== false;
    }

    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    public static function getRandomStr()
    {
        $str = "";
        $baseStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($baseStr) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $baseStr[mt_rand(0, $max)];
        }
        return $str;
    }

    /**
     * 数组转xml
     *     仅支持二维数组
     * @param $data
     * @return string
     */
    public static function arr2xml($data)
    {
        if (!is_array($data) || count($data) <= 0) {
            return '';
        }
        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param $xml
     * @param bool $isArr
     * @return mixed
     */
    public static function xml2arr($xml, $isArr=true)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), $isArr);
        return $result;
    }

}