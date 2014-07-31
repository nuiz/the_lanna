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
use Main\Helper\Image;
use Main\Helper\ResponseHelper;

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
            return ResponseHelper::notFound("Room type not found");
        }
        $entity['thumb'] = Image::instance()->findByRef($entity['thumb'])->toArrayResponse();
        return $entity;
    }

    public function addFood(array $params, ContextInterface $ctx = null){
        $entity = $params;
        $thumb = Image::instance()->add($params['thumb']);
        $entity['thumb'] = $thumb->getDBRef();

        $entity['parent'] = null;
        if(!empty($params['parent_id'])){
            $parent = FolderService::instance()->get($params['parent_id']);
            $parentRef = \MongoDBRef::create("folders", $parent['_id']);
            $entity['parent'] = $parentRef;
            unset($entity['parent_id']);
        }
        $this->collection->insert($entity);

        // Node service update
        NodeService::instance()->addOrEdit($entity, 'service_food');

        return $this->get($entity['_id']);
    }

    public function addRoom(array $params, ContextInterface $ctx = null){
        $entity = $params;
        $thumb = Image::instance()->add($params['thumb']);
        $entity['thumb'] = $thumb->getDBRef();

        $entity['parent'] = null;
        if(!empty($params['parent_id'])){
            $parent = FolderService::instance()->get($params['parent_id']);
            $parentRef = \MongoDBRef::create("folders", $parent['_id']);
            $entity['parent'] = $parentRef;
            unset($entity['parent_id']);
        }
        $this->collection->insert($entity);

        // Node service update
        NodeService::instance()->addOrEdit($entity, 'service_room');

        return $this->get($entity['_id']);
    }

    public function getItemPictures($id, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $select = array("_id", "detail", "feature", "price", "pictures");

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
}