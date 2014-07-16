<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/16/14
 * Time: 3:58 PM
 */

namespace Main\Service;


use Main\DB;

class ContactService {
    private $collection;
    private function __construct(){
        $db = DB::getDB();
        $contacts = $db->contacts;
        $this->collection = $contacts;
    }

    public static function instance(){
        return new self();
    }

    public function get(){
        $entity = $this->collection->findOne();
        if(is_null($entity)){
            $entity = $this->edit(array(
                "phone"=> "088-888-8888",
                "website"=> "www.example.com",
                "email"=> "example@example.com",
                "location"=> array(
                    "lat"=> "18.795095",
                    "lng"=> "98.993213"
                )
            ));
        }

        return $entity;
    }

    public function edit(array $param){
        $entity = $this->collection->findOne();
        if(is_null($entity)){
            $this->collection->insert($param);
        }
        else {
            $this->collection->update(array("_id"=> $entity['_id']), array('$set' => $param));
        }

        return $this->collection->findOne();
    }
}