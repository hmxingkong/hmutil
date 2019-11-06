<?php


namespace hmxingkong\utils\cn;


use hmxingkong\utils\MString;

/**
 * 汉语拼音处理工具类
 * Class MSpelling
 * @package hmxingkong\utils\cn
 */
class MSpelling
{

    /**
     * 拼音转换，数字音调
     *      bā  =>  ba1
     * @param $spell
     * @return mixed|string
     */
    public static function getToneNameFromSpell($spell){
        if(empty($spell)) return '';
        static $tones = false;
        if(!$tones) $tones = self::getTones();

        $specialInitialSyllable = ['j', 'q', 'x', 'y']; //ju qu xu yu qun xun yun
        if(in_array(substr($spell, 0, 2), ['zh', 'ch', 'sh'])){
            $initial = substr($spell, 0, 2); //声母
            $vowel = substr($spell, 2, strlen($spell)-2); //韵母
        }else{
            $initial = substr($spell, 0, 1); //声母
            $vowel = substr($spell, 1, strlen($spell)-1); //韵母
        }
        for($i=5; $i >= 0; $i--){ //音调字母
            for($j=0; $j < 6; $j++){ //音调数组
                if(isset($tones[$j][$i])){
                    $tone = $tones[$j][$i];
                    if(MString::contains($spell, $tone)){
                        if(in_array($initial, $specialInitialSyllable) && $tones[$j][0] == 'u' && mb_strlen($vowel, mb_internal_encoding()) == 1){
                            $name = str_replace($tone, $tones[5][0], $spell);  // v
                        }else{
                            $name = str_replace($tone, $tones[$j][0], $spell);
                        }

                        if($i > 0 && $i < 5){
                            return $name . $i;
                        }
                        else/* if($i == 0 || $i == 5)*/{
                            return $name;
                        }
                    }
                }
            }
        }
        return '';
    }

    /**
     * 拼音转换，书写音调
     *      ba1  =>  bā
     * @param $toneName
     * @return mixed|string
     */
    public static function getSpellFromToneName($toneName){
        if(empty($toneName)) return '';
        static $tones = false;
        if(!$tones) $tones = self::getTones();

        $tGrade = substr($toneName, -1, 1);
        if(!is_numeric($tGrade)){
            //包含单字 v
            if(MString::contains($toneName, $tones[5][0])){
                return str_replace($tones[5][0], $tones[5][5], $toneName);
            }
            //无音调
            return $toneName;
        }
        $toneName = substr($toneName, 0, strlen($toneName)-1);
        foreach($tones as $tone){
            if(MString::contains($toneName, $tone[0])){
                return str_replace($tone[0], $tone[$tGrade], $toneName);
            }
        }
        if(MString::contains($toneName, $tones[5][5])){
            return str_replace($tones[5][5], $tones[5][$tGrade], $toneName);
        }
        return '';
    }

    /**
     * 获取音调符号
     * @return array
     */
    public static function getTones(){
        return [
            ['a', 'ā','á','ǎ','à'],
            ['o', 'ō','ó','ǒ','ò'],
            ['e', 'ē','é','ě','è', 'ê'],
            ['i', 'ī','í','ǐ','ì'],
            ['u', 'ū','ú','ǔ','ù'],
            ['v', 'ǖ','ǘ','ǚ','ǜ', 'ü'],
        ];
    }

    /**
     * 识别声母和韵母
     * @param $spell
     * @return array
     *      [
     *          'overall'=>'',   // 整体认读
     *          'initial'=>'',   // 声母
     *          'dielectric'=>'',// 介母  针对三拼音节 self::getThreeVowelSyllable
     *          'vowel'=>'',     // 韵母
     *          'tone'=>''       // 音调 1~4
     *      ]
     */
    public static function getPartsFromSpell($spell){
        $parts = [ 'overall'=>'', 'initial'=>'', 'dielectric'=>'', 'vowel'=>'', 'tone'=>'' ];
        if(empty($spell)) return $parts;
        $toneName = self::getToneNameFromSpell($spell);
        return self::getPartsFromToneName($toneName);
    }

    /**
     * 识别声母和韵母
     * @param $toneName
     * @return array
     *      [
     *          'overall'=>'',   // 整体认读
     *          'initial'=>'',   // 声母
     *          'dielectric'=>'',// 介母  针对三拼音节 self::getThreeVowelSyllable
     *          'vowel'=>'',     // 韵母
     *          'tone'=>''       // 音调 1~4
     *      ]
     */
    public static function getPartsFromToneName($toneName){
        $parts = [ 'overall'=>'', 'initial'=>'', 'dielectric'=>'', 'vowel'=>'', 'tone'=>'' ];
        if(empty($toneName)) return $parts;
        $tGrade = substr($toneName, -1, 1); //音调
        if(is_numeric($tGrade)){
            $lToneName = substr($toneName, 0, strlen($toneName)-1);
        }else{
            $lToneName = $toneName;
            $tGrade = '';
        }

        //整体认读
        $overallSyllable = self::getOverallSyllable();
        if(in_array($lToneName, $overallSyllable)){
            $parts['overall'] = $lToneName;
            $parts['tone'] = $tGrade;
            return $parts;
        }
        //mlog('toneName: '.$lToneName);

        //声母 / 韵母
        //TODO
        //$initialSyllable = self::getInitialSyllable();
        if(in_array(substr($lToneName, 0, 2), ['zh', 'ch', 'sh'])){
            $initial = substr($lToneName, 0, 2); //声母
            $vowel = substr($lToneName, 2, strlen($lToneName)-2); //韵母
        }else{
            $initial = substr($lToneName, 0, 1); //声母
            $vowel = substr($lToneName, 1, strlen($lToneName)-1); //韵母
        }

        //三拼音节
        $dielectric = '';
        $threeVowelSyllable = self::getThreeVowelSyllable();
        if(in_array($vowel, $threeVowelSyllable)){
            $dielectric = substr($vowel, 0, 1); //介母
            $vowel = substr($vowel, 1, strlen($vowel) - 1); //韵母
        }

        $parts['initial'] = $initial;
        $parts['dielectric'] = $dielectric;
        $parts['vowel'] = $vowel;
        $parts['tone'] = $tGrade;
        return $parts;

        /*
        for($i=1,$len=2; $i<=$len; $i++){
            $initial = substr($lToneName, 0, $i);
            //mlog(sprintf("%s,%s  %s,%s | %s", $initial, in_array($initial, $initialSyllable) ? 'Y' : 'N', $i, substr($lToneName, $i, strlen($lToneName)-$i), json_encode($initialSyllable, JSON_UNESCAPED_UNICODE)));
            if(in_array($initial, $initialSyllable)){
                $parts['initial'] = $initial;
                $parts['vowel'] = substr($lToneName, $i, strlen($lToneName)-$i);
                $parts['tone'] = $tGrade;
                return $parts;
            }
        }
        return $parts;*/
    }

    /**
     * 获取声母
     * @return array
     */
    public static function getInitialSyllable(){
        return [
            'b', 'p', 'm', 'f', 'd', 't', 'n', 'l', 'g', 'k', 'h', 'j', 'q', 'x', 'zh', 'ch', 'sh', 'z', 'c', 's', 'r', 'y', 'w'
        ];
    }

    /**
     * 获取韵母
     * @return array
     */
    public static function getVowelSyllable(){
        // ü => v
        // üe => ve
        // ün => vn
        return [
            'a', 'o', 'e', 'i', 'u', 'v'
            , 'ai', 'ei', 'ui', 'ao', 'ou', 'iu'
            , 'ie', 've', 'er', 'an', 'en', 'in', 'un', 'vn', 'ang', 'eng', 'ing', 'ong'
        ];
    }

    /**
     * 获取整体认读
     * @return array
     */
    public static function getOverallSyllable(){
        return [
            'zhi', 'chi', 'shi'
            , 'zi', 'ci', 'si', 'ri'
            , 'yi', 'wu', 'yu', 'ye', 'yue', 'yuan'
            , 'yin', 'yun', 'ying'
        ];
    }

    /**
     * 获取三拼音节韵母
     * @return array
     */
    public static function getThreeVowelSyllable(){
        // üan  =>  van
        return [
            'ia', 'ua', 'uo', 'uai', 'iao', 'ian', 'iang', 'uan', 'uang', 'iong', 'van'
        ];
    }

}