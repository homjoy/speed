<?php
namespace Libs\Pinyin;
/**
 * 汉字转拼音方法
 * 注意：输入参数编码为GBK编码
 * 
 * @author Icehu
 */

class Util_Pinyin
{

    static $_handle;

    static function load()
    {
        if(null === self::$_handle)
        {
            $path_name=dirname(__FILE__);
            self::$_handle = fopen( $path_name.'/py.dat', 'rb');
//            self::$_handle = fopen( WB_BASE_DIR . '/config/py.dat', 'rb');
        }
        return true;
    }

    private static function get($zh)
    {
        if (strlen($zh) != 2)
        {
            return '';
        }

        self::load();

        if(!self::$_handle)
        {
            return '';
        }

        $high = ord($zh[0]) - 0x81;
        $low  = ord($zh[1]) - 0x40;

        // 计算偏移位置
//        $nz = ($ord0 - 0x81);
        $off = ($high<<8) + $low - ($high * 0x40);
        // 判断 off 值
        if ($off < 0)
        {
            return '';
        }

        fseek(self::$_handle, $off * 8, SEEK_SET);
        $ret = fread(self::$_handle, 8);
        $ret = unpack('a8py', $ret);
        return substr(trim($ret['py']),0,-1);
    }

    public static function convertToPinYin($str)
    {
        $len = strlen($str);
        $ret = '';
        for ($i = 0; $i < $len; $i++)
        {
            if (ord($str[$i]) > 0x80)
            {
                $xx = self::get(substr($str, $i, 2));
                $ret .= ($xx ?  $xx : substr($str, $i, 2));
                $i++;
            }
            else
            {
                $ret .= $str[$i];
            }
        }
        return $ret;
    }

    static function convertToPinYinLimit($str,$limit=16)
    {
        $len = strlen($str);
        $ret = '';
        for ($i = 0; $i < $len; $i++)
        {
            if (ord($str[$i]) > 0x80)
            {
                $xx = self::get(substr($str, $i, 2));
                $add = ($xx ?  $xx : substr($str, $i, 2));
                if( strlen($ret . $add) > $limit )
                {
                    break;
                }
                $ret .= $add;
                $i++;
            }
            else
            {
                if( strlen($ret . $str[$i]) > $limit )
                {
                    break;
                }
                $ret .= $str[$i];
            }
        }
        return $ret;
    }
}
