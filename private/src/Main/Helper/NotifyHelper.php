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
    protected static $apnHelper = null;

    protected static function getApnHelper(){
        if(is_null(self::$apnHelper)){
            self::$apnHelper = new APNHelper(file_get_contents('private/apple/dev.pem'), 'gateway.sandbox.push.apple.com', 2195);
        }
        return self::$apnHelper;
    }

    public static function sendAll($objectId, $header, $message){
        $db = DB::getDB();
        $cDevices = $db->devices->find();
        foreach($cDevices as $item){
            $entity = self::create($objectId, $header, $message, $item['key'], $item['type']);

            $entity['object']['id'] = $entity['object']['_id']->{'$id'};
            $entity['id'] = $entity['_id']->{'$id'};
            //$deepLink = URL::absolute('/notify/'.$entity['id']);
            $objectUrl = URL::absolute('/news/'.$entity['object']['id']);

            // if not admin not push to device
            if(!$item['admit'])
                continue;

            if(is_array($message)){
                $message = $message[$item['lang']];
            }

            if(strlen(utf8_encode($message)) > 120){
                $message = mb_substr($message, 0, 18, 'utf-8').'...';
            }

            $args = array(
                'id'=> $entity['id'],
                'object_id'=> $entity['object']['id']
            );

            if($item['type']=='ios'){
                try {
                    self::getApnHelper()->send($item['key'], $message, $objectUrl);
                }catch (\Exception $e){
                    var_dump($e);
                    exit();
                }
            }
            else{
                $gcmTokens = array($item['key']);
                GCMHerlper::send($gcmTokens, array(
                    'preview_content'=> $message,
                    'args'=> $args
                ));
            }
        }
    }

    public static function create($objectId, $header, $message, $deviceKey, $deviceType){
        $db = DB::getDB();

        if(!($objectId instanceof \MongoId)){
            $objectId = new \MongoId($objectId);
        }

        $now = new \MongoTimestamp();
        $entity = array(
            'preview_header'=> $header,
            'preview_content'=> $message,
            'object'=> array(
                'type'=> 'news',
                '_id'=> $objectId
            ),
            'device'=> array(
                'key'=> $deviceKey,
                'type'=> $deviceType
            ),
            'opened'=> false,
            'create_at'=> $now
        );

        $db->notify->insert($entity);
        return $entity;
    }
}