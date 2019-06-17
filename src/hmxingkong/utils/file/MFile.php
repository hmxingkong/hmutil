<?php


namespace hmxingkong\utils\file;


class MFile
{

    /**
     * 根据路径获取文件名
     * @param $file
     * @param string $separator
     * @return bool|string
     */
    public static function getFileName($file, $separator='/')
    {
        return substr($file, strrpos($file, $separator));
    }

    /**
     * 根据路径获取文件扩展名
     * @param $file
     * @return bool|string
     */
    public static function getFileSuffix($file)
    {
        if(!MString::contains($file, '.')){
            return '';
        }
        $suffix = strtolower(substr($file, strrpos($file, '.')));
        return $suffix;
    }

}