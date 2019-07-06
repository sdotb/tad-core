<?php
namespace Libs;

/**
 *
 */
class Utils
{

    public static function uTS()
    {
        return round(microtime(true)*1000);
    }

    public static function mtRandStr($l = 32, $c = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')
    {
        for ($s = '', $cl = strlen($c)-1, $i = 0; $i < $l; $s .= $c[mt_rand(0, $cl)], ++$i);
        return $s;
    }

}
