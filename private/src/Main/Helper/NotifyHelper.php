<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 8/14/14
 * Time: 5:05 PM
 */

namespace Main\Helper;


use Main\DB;

class NotifyHelper {
    public static function send($title, $message){
        $db = DB::getDB();
        $cDevices = $db->devices->find();
        $gcmTokens = array();
        foreach($cDevices as $item){
            if($item['type']=='ios'){
                APNHelper::send($item['key'], $title, $message);
            }
            else{
                $gcmTokens[] = $item['key'];
            }
        }
        if(count($gcmTokens)>0){
            GCMHerlper::send($gcmTokens, $title, $message);
        }
    }
}