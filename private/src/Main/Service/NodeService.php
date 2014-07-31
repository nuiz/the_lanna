<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 2:24 PM
 */

namespace Main\Service;


use Main\Context\ContextInterface;
use Main\DB;
use Main\Helper\URL;

class NodeService extends BaseService {
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
        $this->db = DB::getDB();
        $this->collection = $this->db->nodes;
    }

    public function addOrEdit($entity, $type){
        $_id = $entity['_id'];
        if(!($_id instanceof \MongoDate)){
            $_id = new \MongoId($_id);
        }

        $entity['type'] = $type;
        unset($entity['_id']);
        $this->collection->update(
            array('_id'=> $_id),
            array('$set'=> $entity),
            array('upsert'=> true));
    }

    public function gets($options = array(), ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $default = array(
            "page"=> 1,
            "limit"=> 15
        );
        $options = array_merge($default, $options);

        $skip = ($default['page']-1)*$default['limit'];
        //$select = array("name", "detail", "feature", "price", "pictures");
        $condition = array('parent'=> null);

        if(isset($options['params']['parent_id'])){
            $parentId = $options['params']['parent_id'];
            if(!($parentId instanceof \MongoId))
                $parentId = new \MongoId($parentId);

            $condition['parent'] = \MongoDBRef::create("folders", $parentId);
        }

        $cursor = $this->collection
            ->find($condition)
            ->limit($default['limit'])
            ->skip($skip);

        $total = $this->collection->count($condition);
        $length = $cursor->count();

        $data = array();
        foreach($cursor as $item){
            $thumbModel = \Main\Helper\Image::instance()->findByRef($item['thumb']);
            $item['thumb'] = $thumbModel->toArrayResponse();

            $item['id'] = $item['_id']->{'$id'};
            unset($item['_id'], $item['parent']);

            $item['node'] = $this->makeNode($item);

            $data[] = $item;
        }

        return array(
            'length'=> $length,
            'total'=> $total,
            'data'=> $data,
            'paging'=> array(
                'page'=> $options['page'],
                'limit'=> $options['limit']
            )
        );
    }

    private function makeNode($item){
        if($item['type']=='service_room' || $item['type']=='service_food'){
            $node = array(
                'pictures'=> URL::absolute('/service/'.$item['id'].'/pictures')
            );
        }
        else {
            $node = array(
                'children'=> URL::absolute('/node/'.$item['id'].'/children')
            );
        }
        return $node;
    }
}