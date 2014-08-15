<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 8/14/14
 * Time: 4:54 PM
 */

namespace Main\Helper;


use JWage\APNS\Certificate;
use JWage\APNS\Client;
use JWage\APNS\Sender;
use JWage\APNS\SocketClient;

class APNHelper {
    protected static $sender;

    public static function getSender(){
        if(is_null(self::$sender)){
            $certificate = new Certificate(file_get_contents('apns.pem'));
            $socketClient = new SocketClient($certificate, 'gateway.push.apple.com', 2195);
            $client = new Client($socketClient);
            self::$sender = new Sender($client);
        }
        return self::$sender;
    }

    public static function send($token, $title, $message){
        $sender = self::getSender();
        $sender->send($token, $title, $sender);
    }
}