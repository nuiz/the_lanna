<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/16/14
 * Time: 3:58 PM
 */

namespace Main\Service;


use Main\Context\Context;
use Main\Context\ContextInterface;
use Main\DB;

class ContactService extends BaseService {
    private $collection;
    protected static $instance = null;

    /** @return self */
    public static function instance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected function __construct(){
        $db = DB::getDB();
        $contacts = $db->contacts;
        $this->collection = $contacts;
    }

    public function get(ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $entity = $this->collection->findOne();
        if(is_null($entity)){
            $entity = $this->insertWhenEmpty($ctx);
        }

        return $entity;
    }

    public function edit(array $param, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $entity = $this->collection->findOne();
        if(is_null($entity)){
            $entity = $this->insertWhenEmpty($ctx);
        }

        $set = array("phone"=> "", "website"=> "", "email"=> "");
        $set = array_intersect_key($param, $set);
        if(count($set)==0){
            return $entity;
        }

        $this->collection->update(array("_id"=> $entity['_id']), array('$set'=> $set));

        return $this->collection->findOne();
    }

    private function insertWhenEmpty(ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $entity = array(
            "phone"=> "088-888-8888",
            "website"=> "http://www.example.com",
            "email"=> "example@example.com"
        );

        $this->collection->insert($entity);

        return $entity;
    }
}