<?php


namespace hmxingkong\utils\file;


use hmxingkong\utils\MString;

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
        //return basename($file);
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

    /**
     * 创建文件
     * @param $fileName
     * @param string $content
     * @return bool
     */
    public static function createFile($fileName, $content='')
    {
        try{
            $file = fopen($fileName, 'w');
            if(!empty($content)){
                fwrite($file, $content);
            }
            return true;
        }finally{
            if(isset($file) && $file){
                fclose($file);
            }
        }
        return false;
    }

    /**
     * 删除文件
     * @param $fileName
     * @return bool
     */
    public static function deleteFile($fileName)
    {
        if(!file_exists($fileName)){
            return true;
        }
        return @unlink($fileName);
    }
}