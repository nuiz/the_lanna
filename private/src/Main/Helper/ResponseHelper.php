<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/30/14
 * Time: 11:54 AM
 */

namespace Main\Helper;


class ResponseHelper {
    public static function notFound($message = "Object not found"){
        return array(
            'code'=> 404,
            'message'=> $message,
            'type'=> 'NotFound'
        );
    }

    public static function error($message, $code = 500, $type = 'Error'){
        return array(
            'code'=> $code,
            'message'=> $message,
            'type'=> $type
        );
    }
} 