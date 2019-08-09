<?php


namespace hmxingkong\utils\network\file;

use hmxingkong\utils\file\MFile;
use hmxingkong\utils\network\MContentType;

/**
 * 网络文件工具类
 * Class MNetFile
 * @package hmxingkong\utils\network\file
 */
class MNetFile
{

    /**
     * @param $fullFilePath
     * @param string $targetFileName :注意，如果路径中的文件名包含中文，pathinfo函数无法正确解析，对于Client端的文件名会缺失，为避免此问题请显示指定该参数
     * @return array
     */
    public static function sendFileToClient($fullFilePath, $targetFileName='')
    {
        $res = ['status' => 0, 'info' => '', 'data' => [
            'fName' => '',
            'fType' => '',
            'fSize' => '',
        ]];

        if(!file_exists($fullFilePath)){
            $res['info'] .= '【不存在的资源】';
            return $res;
        }

        if(!is_readable($fullFilePath)){
            $res['info'] .= '【不可访问的资源】';
            return $res;
        }

        $pInfo = pathinfo($fullFilePath);
        if(!$pInfo){
            $res['info'] .= '【不能识别的资源】';
            return $res;
        }
        if(!isset($pInfo['basename']) || empty($pInfo['basename'])){
            $res['info'] .= '【未识别文件名】';
            return $res;
        }
        if(!isset($pInfo['extension']) || empty($pInfo['extension'])){
            $res['info'] .= '【未识别扩展名】';
            return $res;
        }

        $res['data']['fName'] = $pInfo['basename']; //MFile::getFileName($fullFilePath);
        $res['data']['fType'] = MContentType::getContentType('.' . $pInfo['extension']); //MContentType::getContentType(MFile::getFileSuffix($fullFilePath));
        $res['data']['fSize'] = filesize($fullFilePath);  //此方法只能获取本地文件大小，网络文件参考get_headers

        $fileSize = $res['data']['fSize'];
        $targetFileName = !empty($targetFileName) ? $targetFileName : $pInfo['basename'];
        $contentType = MContentType::getContentType(MFile::getFileSuffix($fullFilePath));

        //超过10M，设置不超时
        if($fileSize / 1024 / 1024 > 10){
            set_time_limit(0);
        }

        if(isset($_SERVER['HTTP_RANGE'])){
            header("HTTP/1.1 206 Partial Content");
            list($name, $range) = explode("=", $_SERVER['HTTP_RANGE']);
            list($seekBegin, $seekEnd) = explode("-", $range);
            if($seekEnd == 0){
                $seekEnd = $fileSize - 1;
            }
        } else {
            header("HTTP/1.1 200 OK");
            $seekBegin = 0;
            $seekEnd = $fileSize - 1;
        }

        header("Content-type: " . $contentType);
        header("Accept-Ranges: bytes");
        header("Content-Length: " . ($seekEnd - $seekBegin + 1));
        header("Content-Disposition: attachment; filename=" . $targetFileName);
        header("Content-Range: bytes " . $seekBegin . "-" . $seekEnd . "/" . $fileSize);

        try{
            $fp = fopen($fullFilePath, 'rb');
            fseek($fp, $seekBegin);
            /*$length = 1024 * 1024;  //1M
            while(!feof($fp) && ($c = fread($fp, $length)) !== false)
            {
                echo $c;
            }*/
            $bufLen = 1024 * 1024;  //1M
            while(!feof($fp))
            {
                $rLen = min($bufLen, $seekEnd - $seekBegin + 1);
                if($rLen <= 0) break;
                $seekBegin += $rLen;
                echo fread($fp, $rLen);

                /*$c = fread($fp, $length);
                if($c===false) break;
                echo $c;
                //strlen($c)."/".$length*/
            }
            ob_flush();
            flush();
        }catch(\Exception $e){
            $res['info'] .= sprintf("【执行遇到异常 -%s】", $e->getMessage());
            return $res;
        }finally{
            if(isset($fp)) try{ fclose($fp); }catch(\Exception $e0){}
        }

        $res['status'] = 1;
        $res['info'] .= "【下载完成】";
        return $res;
    }

}