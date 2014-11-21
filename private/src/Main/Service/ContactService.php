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
use Main\Helper\Image;

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
        if(!is_null($entity['picture'])){
            $pic = Image::instance()->findByRef($entity['picture']);
            $entity['picture'] = $pic->toArrayResponse();
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

        $set = array("phone"=> "", "website"=> "", "email"=> "", "picture"=> "", "fax"=> "");
        $set = array_intersect_key($param, $set);
        if(count($set)==0){
            return $entity;
        }

        if(isset($param['picture'])){
            $pic = Image::instance()->add($param['picture']);
            $set['picture'] = $pic->getDBRef();
        }

        $this->collection->update(array("_id"=> $entity['_id']), array('$set'=> $set));

        return $this->get($ctx);
    }

    private function insertWhenEmpty(ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $entity = array(
            "phone"=> "088-888-8888",
            "fax"=> "053-333-333",
            "website"=> "http://www.example.com",
            "email"=> "example@example.com",
            "picture"=> null
        );

        $this->collection->insert($entity);

        return $entity;
    }
}