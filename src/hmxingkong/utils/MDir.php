<?php


namespace hmxingkong\utils;


class MDir
{

    /**
     * 获取指定目录下的文件
     * @param $path
     * @param string $pattern
     * @param bool $includeDir
     * @return array
     */
    public static function listFiles($path, $pattern = '*', $includeDir=false)
    {
        $tFiles = [];
        $files = glob($path . $pattern, GLOB_MARK);
        if($files === false || empty($files))
            return $tFiles;
        foreach($files as $file){
            if(is_file($file)){
                $tFiles[] = $file;
            }
            else if(is_dir($file)){
                if($includeDir){
                    $tFiles[] = $file;
                }
                $sFiles = MDir::listFiles($file, $pattern, $includeDir);
                $tFiles = array_merge($tFiles, $sFiles);
            }
        }
        return $tFiles;
    }

    /**
     * 创建文件夹
     * @param $pathname
     * @param int $mode
     * @return bool
     */
    public static function mkdir($pathname, $mode=0755)
    {
        if (!is_dir($pathname)) {
            return mkdir($pathname, $mode, true);
        }
        return false;
    }
}