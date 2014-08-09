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
use Main\Helper\ArrayHelper;
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

    public function addOrEdit($entity, $type, ContextInterface $ctx = null){
        $_id = $entity['_id'];
        if(!($_id instanceof \MongoId)){
            $_id = new \MongoId($_id);
        }

        $seq = 1;
        $old = $this->collection->findOne(array('_id'=> $_id));
        if(is_null($old)){
            $parentField = $entity['parent'];
            $maxCursor = $this->collection->find(array('parent'=> $parentField))->sort(array("seq"=> -1))->limit(1);
            if($maxCursor->count(true) > 0){
                $maxEntity = $maxCursor->getNext();
                $seq = $maxEntity['seq']+1;
            }
        }

        $entity['seq'] = $seq;
        $entity['type'] = $type;
        unset($entity['_id']);
        $entity = ArrayHelper::ArrayGetPath($entity);
        $this->collection->update(
            array('_id'=> $_id),
            array('$set'=> $entity),
            array('upsert'=> true));
    }

    public function delete($_id){
        if(!($_id instanceof \MongoDate)){
            $_id = new \MongoId($_id);
        }
        $this->collection->remove(array('_id'=> $_id));
    }

    public function gets($options = array(), ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $default = array(
            "page"=> 1,
            "limit"=> 15
        );
        $options = array_merge($default, $options);

        $skip = ($options['page']-1)*$options['limit'];
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
            ->limit((int)$options['limit'])
            ->skip((int)$skip)
            ->sort(array('seq'=> -1));

        $total = $this->collection->count($condition);
        $length = $cursor->count(true);

        $data = array();
        foreach($cursor as $item){
            $thumbModel = \Main\Helper\Image::instance()->findByRef($item['thumb']);
            $item['thumb'] = $thumbModel->toArrayResponse();

            // children length
            $item['children_length'] = $this->getChildrenLength($item['_id']);

            $item['id'] = $item['_id']->{'$id'};
            unset($item['_id'], $item['parent']);

            $item['node'] = $this->makeNode($item);

            if(!$ctx->isAdminConsumer()){
                $item['name'] = $item['name'][$ctx->getLang()];
                if(isset($item['detail'])){
                    $item['detail'] = $item['detail'][$ctx->getLang()];
                }
            }
            if(isset($item['pictures'])){
                unset($item['pictures']);
            }

            $data[] = $item;
        }

        return array(
            'length'=> $length,
            'total'=> $total,
            'data'=> $data,
            'paging'=> array(
                'page'=> (int)$options['page'],
                'limit'=> (int)$options['limit']
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

    public function getChildrenLength($_id){
        if(!($_id instanceof \MongoDate)){
            $_id = new \MongoId($_id);
        }

        return $this->collection->find(array('parent'=> \MongoDBRef::create("folders", $_id)))->count();
    }
}