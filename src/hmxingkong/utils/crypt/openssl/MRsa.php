<?php


namespace hmxingkong\utils\crypt\openssl;

/**
 * Class RSA
 * @package hmxingkong\utils\crypt\openssl
 */
class RSA
{

    /**
     * 使用公钥加密数据
     * @param $data
     * @param $key
     * @param int $padding : OPENSSL_PKCS1_PADDING, OPENSSL_SSLV23_PADDING, OPENSSL_PKCS1_OAEP_PADDING, OPENSSL_NO_PADDING
     * @return string
     */
    public static function publicKeyEncrypt($data, $key, $padding = OPENSSL_PKCS1_PADDING)
    {
        $crypted = '';
        try{
            //使用公钥加密数据
            //https://www.php.net/manual/zh/function.openssl-public-encrypt.php
            if(!openssl_public_encrypt($data ,$crypted , $key, $padding)){
                return $crypted;
            }
        }finally{

        }
        return base64_encode($crypted);
    }

    /**
     * 使用私钥解密数据
     * @param $data
     * @param $key
     * @param int $padding : OPENSSL_PKCS1_PADDING, OPENSSL_SSLV23_PADDING, OPENSSL_PKCS1_OAEP_PADDING, OPENSSL_NO_PADDING
     * @return string
     */
    public static function privateKeyDecrypt($data, $key, $padding = OPENSSL_PKCS1_PADDING)
    {
        $decrypted  = '';
        try{
            //使用私钥解密数据
            //https://www.php.net/manual/zh/function.openssl-private-decrypt.php
            if(!openssl_private_decrypt(base64_decode($data),$decrypted , $key, $padding)){
                return $decrypted ;
            }
        }finally{

        }
        return $decrypted ;
    }

    /**
     * 使用私钥加密数据
     * @param $data
     * @param $key
     * @param int $padding : OPENSSL_PKCS1_PADDING, OPENSSL_NO_PADDING
     * @return string
     */
    public static function privateKeyEncrypt($data, $key, $padding = OPENSSL_PKCS1_PADDING)
    {
        $crypted = '';
        try{
            //使用公钥加密数据
            //https://www.php.net/manual/zh/function.openssl-private-encrypt.php
            if(!openssl_private_encrypt($data ,$crypted , $key, $padding)){
                return $crypted;
            }
        }finally{

        }
        return base64_encode($crypted);
    }

    /**
     * 使用公钥解密数据
     * @param $data
     * @param $key
     * @param int $padding : OPENSSL_PKCS1_PADDING, OPENSSL_NO_PADDING.
     * @return string
     */
    public static function publicKeyDecrypt($data, $key, $padding = OPENSSL_PKCS1_PADDING)
    {
        $decrypted  = '';
        try{
            //使用公钥解密数据
            //https://www.php.net/manual/zh/function.openssl-public-decrypt.php
            if(!openssl_public_decrypt( base64_decode($data),$decrypted , $key, $padding)){
                return $decrypted ;
            }
        }finally{

        }
        return $decrypted ;
    }

    /**
     * 使用私钥签名
     * @param $data
     * @param $priKey
     * @param string $signatureAlg : openssl_get_md_methods()  https://www.php.net/manual/zh/function.openssl-get-md-methods.php
     * @return string
     */
    public static function sign($data, $priKey, $signatureAlg = 'sha1WithRSAEncryption')
    {
        $signature = '';
        try{
            //使用私钥签名
            //https://www.php.net/manual/zh/function.openssl-sign.php
            //openssl_get_privatekey()
            //openssl_free_key
            if(!openssl_sign($data ,$signature , $priKey, $signatureAlg)){
                return $signature;
            }
        }finally{

        }
        return base64_encode($signature);
    }

    /**
     * 使用公钥验签
     * @param $data
     * @param $signature
     * @param $pubKey
     * @param string $signatureAlg : openssl_get_md_methods()  https://www.php.net/manual/zh/function.openssl-get-md-methods.php
     * @return string
     */
    public static function verify($data, $signature, $pubKey, $signatureAlg = 'sha1WithRSAEncryption')
    {
        try{
            //使用公钥验签
            //https://www.php.net/manual/zh/function.openssl-verify.php
            //openssl_get_publickey()
            //openssl_free_key
            $res = openssl_verify ($data ,base64_decode($signature) , $pubKey, $signatureAlg);
            return $res == -1 ? 0 : $res;
        }finally{

        }
        return false;
    }

    /**
     * 生成密钥对（公钥/私钥）
     * @param int $privateKeyBits : 私钥字节数  512 1024  2048  4096等
     * @param string $privKeyPass
     * @param int $numberOfDays
     * @param string $digestAlg
     * @param array $dn
     * @return array
     */
    public static function generateKeys($privateKeyBits = 4096, $privKeyPass = '', $numberOfDays = 365, $digestAlg = 'sha512', $dn = [
        "countryName" => "CN",
        "stateOrProvinceName" => "GD",
        "localityName" => "GZ",
        "organizationName" => "HM",
        "organizationalUnitName" => "HM",
        "commonName" => "hmxingkong",
        "emailAddress" => "service@hmxingkong.com"])
    {
        $tKey = [
            'priKey' => '',
            'pubKey' => '',
        ];

        $config = array(
            "digest_alg" => $digestAlg,
            "private_key_bits" => $privateKeyBits, //私钥字节数  512 1024  2048  4096等
            "private_key_type" => OPENSSL_KEYTYPE_RSA, //加密类型，在创建CSR时应该使用哪些扩展。可选值有 OPENSSL_KEYTYPE_DSA, OPENSSL_KEYTYPE_DH, OPENSSL_KEYTYPE_RSA 或 OPENSSL_KEYTYPE_EC . 默认值是 OPENSSL_KEYTYPE_RSA .
        );

        try{

            //生成证书
            //https://www.php.net/manual/zh/function.openssl-pkey-new.php
            if(($privKey = openssl_pkey_new($config)) === false){
                return $tKey;
            }

            //生成一个 CSR
            //https://www.php.net/manual/zh/function.openssl-csr-new.php
            if(($csr = openssl_csr_new($dn, $privKey, $config)) === false){
                return $tKey;
            }

            //从给定的 CSR 生成一个x509证书资源
            //https://www.php.net/manual/zh/function.openssl-csr-sign.php
            if(($sSecret = openssl_csr_sign($csr, null, $privKey, $numberOfDays, $config)) === false){
                return $tKey;
            }

            //将 x509 以PEM编码的格式导出到 $output 变量中（导出证书）
            //https://www.php.net/openssl-x509-export
            if(!openssl_x509_export($sSecret, $csrKey)){
                return $tKey;
            }

            //将 x509 以 PKCS#12 文件格式导出到 $out 变量中（导出密钥）
            //https://www.php.net/manual/zh/function.openssl-pkcs12-export.php
            if(!openssl_pkcs12_export($sSecret, $privateKey, $privKey, $privKeyPass)){
                return $tKey;
            }

            //将pkcs12提供的PKCS#12证书存储区解析到 $certs 变量中（获取私钥）
            //https://www.php.net/manual/zh/function.openssl-pkcs12-read.php
            if(!openssl_pkcs12_read($privateKey, $certs, $privKeyPass)){
                return $tKey;
            }

            //从 certificate 中解析公钥（获取公钥）
            //https://www.php.net/manual/en/function.openssl-pkey-get-public.php
            if(($pubKey = openssl_pkey_get_public($csrKey)) === false){
                return $tKey;
            }

            //返回包含密钥详情的数组
            //https://www.php.net/manual/zh/function.openssl-pkey-get-details.php
            if(($keyData = openssl_pkey_get_details($pubKey)) === false){
                return $tKey;
            }

            $tKey['priKey'] = $certs['pkey'];
            $tKey['pubKey'] = $keyData['key'];

        }finally{

            //数释放由 openssl_pkey_new() 创建的私钥
            //https://www.php.net/manual/zh/function.openssl-free-key.php
            //openssl_free_key($privKey);
            //https://www.php.net/manual/zh/function.openssl-pkey-free.php
            openssl_pkey_free($privKey);

        }

        return $tKey;
    }

}