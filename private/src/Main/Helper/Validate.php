<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/29/14
 * Time: 11:34 AM
 */

namespace Main\Helper;


class Validate {
    public static function isIdFormat($value){
        $regex = '/^[0-9a-z]{24}$/';
        if (preg_match($regex, $value))
        {
            return true;
        }
        return false;
    }
}