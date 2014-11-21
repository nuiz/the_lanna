<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 1:50 PM
 */

namespace Main\Service;


use Main\Context\ContextInterface;
use Main\DB;
use Main\Helper\ArrayHelper;
use Main\Helper\Image;
use Main\Helper\ResponseHelper;
use Main\Helper\URL;

class ServiceService extends BaseService {
    private $collection;

    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct(){
        $this->db = DB::getDB();
        $this->collection = $this->db->services;
    }

    public function get($_id, ContextInterface $ctx = null){
        if(!($_id instanceof \MongoId)){
            $_id = new \MongoId($_id);
        }
        $entity = $this->collection->findOne(array('_id'=> $_id));
        if(is_null($entity)){
            return ResponseHelper::notFound("Service not found");
        }

        // language
        if(!$ctx->isAdminConsumer()){
            $entity['name'] = $entity['name'][$ctx->getLang()];
            $entity['detail'] = $entity['detail'][$ctx->getLang()];
        }

        $picsRef = $entity['pictures'];
        //$item['pictures'] = array();
        unset($entity['pictures']);

        foreach($picsRef as $key=> $picRef){
            $img = \Main\Helper\Image::instance()->findByRef($picRef);
            if($key==0){
                $entity['pictures'] = array();
                $entity['thumb'] = $img->toArrayResponse();
            }
            $entity['pictures'][] = $img->toArrayResponse();
        }

        // id
        $entity['id'] = $entity['_id']->{'$id'};
        unset($entity['_id']);

        $entity['node'] = array(
            'pictures'=> URL::absolute('/service/'.$entity['id'].'/pictures')
        );

        return $entity;
    }

    public function add(array $params, ContextInterface $ctx = null){
        $entity = $params;
//        $thumb = Image::instance()->add($params['thumb']);
//        $entity['thumb'] = $thumb->getDBRef();

        if(!isset($params['pictures']) || count($params['pictures']) == 0){
            return ResponseHelper::notFound("Require pictures");
        }

        $entity['parent'] = null;
        if(isset($params['parent_id'])){
            if($params['parent_id'] == 0 || $params['parent_id'] == null){
                $parentRef = null;
            }
            else {
                $parent = $this->get($params['parent_id']);
                if(is_null($parent)){
                    return ResponseHelper::notFound("Not found parent");
                }

                $parent['_id'] = new \MongoId($params['parent_id']);
                $parentRef = \MongoDBRef::create("folders", $parent['_id']);
            }
            $entity['parent'] = $parentRef;
            unset($entity['parent_id']);
        }
//        if(!isset($set['price']) || $set['price'] == 0 || $set['price'] == ''){
//            $set['price'] = null;
//        }

        $pictures = $params['pictures'];
        $entity['pictures'] = array();
        foreach($pictures as $picture){
            $picModel = \Main\Helper\Image::instance()->add($picture);
            $entity['pictures'][] = $picModel->getDBRef();
        }

        $entity['type'] = !isset($entity['price']) || trim($entity['price'])=='' || $entity['price']=='0'? 'service_room': 'service_food';
        $this->collection->insert($entity);

        // Node service update
        NodeService::instance()->addOrEdit($entity, $entity['type']);

        return $this->get($entity['_id'], $ctx);
    }

    public function edit($_id, array $params, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        if(!($_id instanceof \MongoId)){
            $_id = new \MongoId($_id);
        }

        $entity = array_intersect_key($params, array_flip(['name', 'detail', 'price']));

        if(isset($params['parent_id'])){
            if($params['parent_id'] === 0 || $params['parent_id'] === null){
                $parentRef = null;
            }
            else {
                $parent = $this->get($params['parent_id']);
                if(is_null($parent)){
                    return ResponseHelper::notFound("Not found parent");
                }

                $parent['_id'] = new \MongoId($params['parent_id']);
                $parentRef = \MongoDBRef::create("folders", $parent['_id']);
            }
            $entity['parent'] = $parentRef;
            unset($entity['parent_id']);
        }
        if(isset($params['thumb'])){
            $thumb = Image::instance()->add($params['thumb']);
            $entity['thumb'] = $thumb->getDBRef();
        }

        if(isset($set['price']) &&($set['price'] == 0 || $set['price'] == '')){
            $entity['price'] = null;
        }

        // unset pictures for protected edit pictures
        unset($entity["pictures"]);
        $entity['type'] = !isset($entity['price']) || trim($entity['price'])=='' || $entity['price']=='0'? 'service_room': 'service_food';

        $entity = ArrayHelper::ArrayGetPath($entity);

        $this->collection->update(array('_id'=> $_id), array('$set'=> $entity));

        $entity = $this->collection->findOne(array('_id'=> $_id));
        $entity['_id'] = $_id;
        unset($entity['id']);

        // Node service update
        NodeService::instance()->addOrEdit($entity, $entity['type'], $ctx);

        return $this->get($_id, $ctx);
    }

    public function delete($_id, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        if(!($_id instanceof \MongoId)){
            $_id = new \MongoId($_id);
        }

        $this->collection->remove(array("_id"=> $_id));

        // delete in node
        NodeService::instance()->delete($_id);

        return array("success"=> true);
    }

    public function getItemPictures($id, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $select = array("_id", "detail", "price", "pictures");

        if(!($id instanceof \MongoId)){ $id = new \MongoId($id); }

        $item = $this->collection->findOne(array('_id'=> $id), $select);

        // hard code for pictures array
        if(!isset($item['pictures'])){
            $item['pictures'] = array();
        }
        // end hard code for pictures array

        $pictures = array();
        $picsRef = $item['pictures'];
        //$item['pictures'] = array();
        unset($item['pictures']);
        foreach($picsRef as $key=> $picRef){
            $img = Image::instance()->findByRef($picRef);
            $pictures[] = $img->toArrayResponse();
        }

        return array(
            'length'=> count($pictures),
            'total'=> count($pictures),
            'data'=> $pictures,
            'paging'=> array(
                'page'=> 1,
                'limit'=> 15
            )
        );
    }

    public function addPictures($id, $pictures = array(), ContextInterface $ctx = null){
        if(!($id instanceof \MongoId)){ $id = new \MongoId($id); }

        $data = array();
        foreach($pictures as $picture){
            $picModel = \Main\Helper\Image::instance()->add($picture);
            $picDBRef = $picModel->getDBRef();
            $this->collection->update(array("_id"=> $id), array(
                '$push'=> array('pictures'=> $picDBRef)
            ));
            $data[] = $picModel->toArrayResponse();
        }
        return $data;
    }

    public function deletePictures($id, $params, ContextInterface $ctx = null){
        if(!($id instanceof \MongoId)){ $id = new \MongoId($id); }

        $data = array();
        foreach($params['id'] as $picId){
            $picDBRef = \MongoDBRef::create("pictures", new \MongoId($picId));
            $this->collection->update(array("_id"=> $id), array(
                '$pull'=> array('pictures'=> $picDBRef)
            ));
            $data[] = $picId;
        }
        return $data;
    }
}