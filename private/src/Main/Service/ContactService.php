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

    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
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

        if(isset($entity['translations'][$ctx->getLang()])){
            $entityTranslation = $entity['translations'][$ctx->getLang()];
            unset($entity['translations']);
            $entity = array_merge($entity, $entityTranslation);
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

        $set = array("website"=> "", "email"=> "", "location.lat"=> "", "location.lng"=> "", "info"=> "");
        $set = array_intersect($set, $param);

        $translationSet = array("info"=> "");
        $translationSet[$ctx->getLang()] = array_intersect($translationSet, $param);
        //if($this->)


        $set['translations.'.$ctx->getLang()] = array(
            "info"=> $param["info"]
        );
        $this->collection->update(array("_id"=> $entity['_id']), array('$set'=> $set));

        return $this->collection->findOne();
    }

    private function insertWhenEmpty(ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $entity = array(
            "phone"=> "088-888-8888",
            "website"=> "www.example.com",
            "email"=> "example@example.com",
            "location"=> array(
                "lat"=> "18.795095",
                "lng"=> "98.993213"
            ),
            "info"=> "example info"
        );

        $defaultLang = $this->ctx->getDefaultLang();
        $entity['translations'] = array(
            $defaultLang => array(
                "info"=> $entity["info"]
            )
        );

        return $this->collection->insert($entity);
    }
}