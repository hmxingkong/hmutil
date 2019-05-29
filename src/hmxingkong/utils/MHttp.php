<?php


namespace hmxingkong\utils;


/**
 * http/https相关工具类
 * Class MHttp
 * @package hmxingkong\utils
 */
class MHttp
{

    /**
     * 获取随机IPV4地址
     * @return string
     */
    public static function randIp()
    {
        return implode(".", [
            round(rand(600000, 2550000) / 10000),
            round(rand(600000, 2550000) / 10000),
            round(rand(600000, 2550000) / 10000)
        ]);
    }

    /**
     * 获取伪装参数
     * @param string $referer
     * @param string $userAgent
     * @param bool $randIp
     * @return array
     */
    public function getPretendArgs($referer='', $userAgent='', $randIp=true)
    {
        $curlOptions = [];
        if($referer) $curlOptions[CURLOPT_REFERER] = empty($referer) ? 'https://www.baidu.com/' : $referer;
        if($randIp) $curlOptions[CURLOPT_HTTPHEADER] = array('X-FORWARDED-FOR:'.$this->randIp(), 'CLIENT-IP:'.$this->randIp());
        $curlOptions[CURLOPT_USERAGENT] = empty($userAgent) ? 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36' : $userAgent;
        return $curlOptions;
    }

    /**
     * 发起curl请求
     *
     * @param $url
     * @param array $reqData
     * @param bool $isPost
     * @param array $header
     * @param array $cookie:  Cookie参数
     * @param array $pretendArgs:  伪装参数
     * @param int $dataType: 返回数据的数据格式  0 不处理  1 Json  2 xml
     * @param int $requestTimeout : 请求超时时间
     * @param int $connectionTimeout : 连接超时时间
     * @param int $isVerifyPeer : 验证证书
     * @param int $isVerifyHost : 验证主机名
     * @param bool $obtainHeader : 是否获取header信息（header包含cookie）
     * @return array
     */
    public static function doRequest($url, $reqData, $isPost = false
        , $header = []
        , $cookie = []
        , $pretendArgs=[]
        , $dataType = 0
        , $requestTimeout = 10
        , $connectionTimeout = 30
        , $isVerifyPeer = 0
        , $isVerifyHost = 0
        , $obtainHeader = false)
    {
        $res = ['status' => 0, 'info' => '', 'data' => [], 'rawData' => '', 'header'=>[]];
        if (empty($url)) {
            $res['info'] = '【URL】不能为空';
            return $res;
        }

        //设置参数：
        $curlOptions = array(
            CURLOPT_HEADER => $obtainHeader,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            // -----------请确保启用以下两行配置------------
            CURLOPT_SSL_VERIFYPEER => $isVerifyPeer, //验证证书
            CURLOPT_SSL_VERIFYHOST => $isVerifyHost, //验证主机名
            // -----------否则会存在被窃听的风险------------
            CURLOPT_SSLVERSION => 1,
            CURLOPT_TIMEOUT => $requestTimeout, //超时时间(s)
            CURLOPT_CONNECTTIMEOUT => $requestTimeout,
            CURLOPT_CONNECTTIMEOUT_MS => $connectionTimeout * 1000,
        );

        if(!empty($cookie)){
            $curlOptions[CURLOPT_COOKIE] = is_string($cookie) ? $cookie : http_build_query($cookie, null, ';');
        }

        //用于伪装的参数
        if (!empty($pretendArgs)) {
            //$curlOptions = array_merge($curlOptions, $pretendArgs);  //不能用array_merge
            foreach ($pretendArgs as $k => $v) {
                $curlOptions[$k] = $v;
            }
        }

        if (!empty($header)) {
            if (isset($curlOptions[CURLOPT_HTTPHEADER]) && is_array($curlOptions[CURLOPT_HTTPHEADER])) {
                $oHeader = $curlOptions[CURLOPT_HTTPHEADER];
                foreach ($header as $k => $v) {
                    $oHeader[$k] = $v;
                }
                $curlOptions[CURLOPT_HTTPHEADER] = $oHeader;
            } else {
                $curlOptions[CURLOPT_HTTPHEADER] = $header;
            }
        }

        if ($isPost) {
            $curlOptions[CURLOPT_POST] = 1;
            $curlOptions[CURLOPT_URL] = $url;
            if (!empty($reqData)) {
                //数据的数据格式  0 不处理  1 Json  2 xml
                switch($dataType){
                    case 0: $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($reqData); /*把数据urlencode后，注入参数*/ break;
                    case 1: $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($reqData); /*把数据urlencode后，注入参数*/ break;
                    case 2: $curlOptions[CURLOPT_POSTFIELDS] = $reqData; break;
                    default: break;
                }
            }
        } else {
            //$curlOptions[CURLOPT_GET] = 1;
            if (!empty($reqData)) {
                $curlOptions[CURLOPT_URL] = $url . '?' . http_build_query($reqData); //把数据urlencode后，注入参数
            } else {
                $curlOptions[CURLOPT_URL] = $url;
            }
        }

        try {
            $curl = curl_init();
            curl_setopt_array($curl, $curlOptions);
            $rawData = curl_exec($curl);
            if($obtainHeader && MString::startWith($rawData, 'HTTP')){  //HTTP/1.1 200 OK
                $delimiter = "\r\n\r\n";
                $rawData = explode($delimiter, $rawData);
                $strHeader = $rawData[0]; unset($rawData[0]);
                $rawData = implode($delimiter, $rawData);
                $header = self::parseHeader($strHeader);
                //echo "tHeader:\n".print_r($header, true)."\n\n";
                $res['header'] = $header;
            }

            //$info = curl_getinfo($curl);
            if (curl_errno($curl)) {
                $res['info'] .= sprintf("请求错误【errno:%s】【%s】", curl_errno($curl), curl_error($curl));
                return $res;
            }

            //数据的数据格式  0 不处理  1 Json  2 xml
            $respData = '';
            switch($dataType){
                case 0: $respData = $rawData; break;
                case 1: $respData = json_decode($rawData, true); break;
                case 2: $respData = MString::xml2arr($rawData, true); break;
                default: break;
            }

            $res['info'] = "【请求结束】";
            $res['status'] = !empty($respData);
            $res['data'] = $respData;
            $res['rawData'] = $rawData;
            return $res;

        } catch (\Exception $e) {
            $res['info'] = sprintf("【请求遇到错误 -%s】", $e->getMessage());
            return $res;
        } finally {
            if (isset($curl)) {
                try {
                    curl_close($curl);
                } catch (\Exception $e) {
                }
            }
        }
    }

    /**
     * 解析请求头
     * @param $strHeader
     * @return array
     */
    public static function parseHeader($strHeader)
    {
        $tHeader = [
            'headers' => [],
            'cookies' => [],
            'keyedCookies' => [],
            'rawHeader' => $strHeader,
        ];

        $headers = array_map('trim', explode("\r\n", $strHeader));
        //preg_match("/Set\-Cookie:(\ )*([^\r\n]+)/i", $header, $cookie);
        foreach($headers as $headerItem){
            //HTTP/1.1 200 OK
            if(MString::startWith($headerItem, "HTTP")){
                //ignore
            }
            //cookie
            //setcookie ( string $name [, string $value = "" [, int $expires = 0 [, string $path = "" [, string $domain = "" [, bool $secure = FALSE [, bool $httponly = FALSE ]]]]]] ) : bool
            elseif(MString::startWith($headerItem, "Set-Cookie")){
                $cookie = ['name'=>'', 'value'=>'', 'expirs'=>'', 'path'=>'', 'domain'=>'', 'secure'=>false, 'httponly'=>false];
                $cookieItems = array_map('trim', explode(";", str_replace("Set-Cookie:", "", $headerItem)));
                foreach($cookieItems as $cookieItem){
                    if(MString::contains($cookieItem, "=")){
                        list($lKey, $lValue) = array_map('trim', explode("=", $cookieItem));
                        if($lValue == '""') $lValue = '';
                        if($lKey == 'Domain') $cookie['domain'] = $lValue;
                        elseif($lKey == 'Path') $cookie['path'] = $lValue;
                        elseif($lKey == 'Expires') $cookie['expirs'] = $lValue;
                        else{
                            $cookie['name'] = $lKey;
                            $cookie['value'] = $lValue;
                        }
                    }
                    else{
                        if($cookieItem == 'Secure') $cookie['secure'] = true;
                        if($cookieItem == 'HttpOnly') $cookie['httponly'] = true;
                    }
                }
                $tHeader['cookies'][] = $cookie;
                $tHeader['keyedCookies'][$cookie['name']] = $cookie['value'];
            }
            elseif(MString::contains($headerItem, ":")){
                //list($lKey, $lValue) = explode(":", $headerItem); //对应未列出来的项会丢失
                $lItems = explode(":", $headerItem);
                $lKey = trim($lItems[0]); unset($lItems[0]);
                $lValue = trim(implode(":", $lItems));
                $tHeader['headers'][trim($lKey)] = $lValue;
            }
            else{
                //TODO ignore ?
            }
        }
        return $tHeader;
    }

}