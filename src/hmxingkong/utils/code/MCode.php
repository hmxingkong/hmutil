<?php


namespace hmxingkong\utils\code;

/**
 * Class MCode
 * @package hmxingkong\utils\code
 */
class MCode
{

    /**
     * 去除给定代码内容的注释
     * @param $content
     * @return string|string[]|null
     */
    public static function removeComment($content){
        //统一换行符
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        //替换注释内容
        return preg_replace("/(\/\*((?!\*\/).)*\*\/)|(\/\/.*?\n)/s", '', $content);
    }

}