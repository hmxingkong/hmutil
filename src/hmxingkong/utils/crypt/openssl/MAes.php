<?php


namespace hmxingkong\utils\crypt\openssl;

/**
 * Class MAes
 * @package hmxingkong\utils\crypt\openssl
 */
class MAes
{

    /**
     * @param string $data
     * @param string $method : 密码学方式。openssl_get_cipher_methods() 可获取有效密码方式列表
     * @param string $key
     * @param string $iv
     * @param int $options : 以下标记的按位或： OPENSSL_RAW_DATA 、 OPENSSL_ZERO_PADDING
     * @param string $tag
     * @param string $aad
     * @param int $tag_length
     * @return string
     */
    public static function encrypt($data, $method='AES-128-ECB', $key, $iv = "", $options = OPENSSL_RAW_DATA, $tag = null, $aad = "", $tag_length = 16)
    {
        // openssl_encrypt 加密不同Mcrypt，对秘钥长度要求，超出16加密结果不变 ?
        //bin2hex
        if($tag == null){
            $crypted = openssl_encrypt($data, $method, $key, $options, $iv);
        }else{
            $crypted = openssl_encrypt($data, $method, $key, $options, $iv, $tag, $aad, $tag_length);
        }
        return base64_encode($crypted);
    }

    /**
     * @param string $data
     * @param string $key
     * @param $method : 密码学方式。openssl_get_cipher_methods() 可获取有效密码方式列表
     * @param int $options : 以下标记的按位或： OPENSSL_RAW_DATA 、 OPENSSL_ZERO_PADDING
     * @param string $iv
     * @param string $tag
     * @param string $aad
     * @return string
     */
    public static function decrypt($data, $method='AES-128-ECB', $key, $iv = "", $options = OPENSSL_RAW_DATA, $tag = null,  $aad = "")
    {
        //aes-256-gcm
        //hex2bin
        //$ciphers = openssl_get_cipher_methods();
        if($tag == null){
            $decrypted = openssl_decrypt(base64_decode($data), $method, $key, $options, $iv);
        }else{
            $decrypted = openssl_decrypt(base64_decode($data), $method, $key, $options, $iv, $tag, $aad);
        }
        return $decrypted;
    }

}