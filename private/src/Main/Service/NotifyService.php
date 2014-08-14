<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 8/13/14
 * Time: 5:12 PM
 */

namespace Main\Service;


class NotifyService extends BaseService {
    protected static $instance = null;

    /** @return self */
    public static function instance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function send($id, $type, $message){
        if(!($id instanceof \MongoId)){
            $id = new \MongoId($id);
        }

        $length = mb_strlen($message, 'UTF-8');
    }
}