<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/31/14
 * Time: 2:32 PM
 */

namespace Main\Service;


use Main\DB;

class DevService extends BaseService {
    /** @return self */
    public static function instance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected function __construct(){
        $this->db = DB::getDB();
        //$this->collection = $this->db->feed;
    }

    public function syncNode(){
        $nodeC = $this->db->nodes;
        $folderC = $this->db->folders;
        $serviceC = $this->db->services;

        $folders = $folderC->find();
        foreach($folders as $key => $value){
            $set = $value;
            unset($set['_id']);
            $set['type'] = "folder";
            $nodeC->update(array('_id'=> $value['_id']), array('$set'=> $set), array('upsert'=> 1));
        }

        $services = $serviceC->find();
        foreach($services as $key => $value){
            $set = $value;
            unset($set['_id']);
            if(isset($set['price'])){
                $set['type'] = "service_food";
            }
            else {
                $set['type'] = "service_room";
            }
            $nodeC->update(array('_id'=> $value['_id']), array('$set'=> $set), array('upsert'=> 1));
        }
    }
}