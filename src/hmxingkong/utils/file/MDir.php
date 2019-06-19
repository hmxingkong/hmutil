<?php


namespace hmxingkong\utils\file;

use hmxingkong\utils\MString;

/**
 * Class MDir
 * @package hmxingkong\utils\file
 */
class MDir
{
    /**
     * 文件类型
     */
    const TYPE_FILE = 'FILE';

    /**
     * 文件夹类型
     */
    const TYPE_DIR = 'FLODER';


    /**
     * 获取指定目录下的文件（递归）
     * @param $path
     * @param string $pattern
     * @param string $type
     * @param bool $recursive
     * @param callable $callback($path, $file, $type, $total, $curIdx)
     * @return array :指定$callback参数时，返回空数组，否则返回文件列表
     */
    public static function listFiles($path, $pattern = '*', $type=MDir::TYPE_FILE, $recursive=true, callable $callback=null)
    {
        $tFiles = [];
        if(!MString::endWith($path, '/'))
            $path .= '/';

        $files = [];
        switch($type){
            case MDir::TYPE_FILE: $files = glob($path . $pattern, GLOB_MARK); break;
            case MDir::TYPE_DIR: $files = glob($path . $pattern, GLOB_ONLYDIR); break;
            default: break;
        }
        if($files === false || empty($files))
            return $tFiles;

        $total = count($files);
        $curIdx = 0;
        foreach($files as $file){
            if(is_file($file)){
                if($callback){
                    $callback($path, $file, MDir::TYPE_FILE, $total, ++$curIdx);
                }else{
                    $tFiles[] = $file;
                }
            }
            else if(is_dir($file)){
                if($callback){
                    $callback($file, '', MDir::TYPE_DIR, $total, ++$curIdx);
                }else{
                    $tFiles[] = $file;
                }
                if($recursive){
                    $sFiles = MDir::listFiles($file, $pattern, $type, $recursive, $callback);
                    if($callback){
                        //IGNORE
                    }else{
                        $tFiles = array_merge($tFiles, $sFiles);
                    }
                }
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
        if(file_exists($pathname)){
            if(is_dir($pathname)){
                return true;
            }
            return false;
        }
        return mkdir($pathname, $mode, true);
    }
}