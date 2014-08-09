<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/18/14
 * Time: 5:11 PM
 */

namespace Main\Service;


use Main\Context\ContextInterface;
use Main\DataModel\Image;
use Main\DB;
use Main\Helper\ArrayHelper;
use Main\Helper\ResponseHelper;
use Main\Helper\URL;

class RoomTypeService extends BaseService {
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
        $this->collection = $this->db->roomtypes;
    }

    public function get($id, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $select = array("name", "detail", "feature", "price", "pictures", "thumb");

        if(!($id instanceof \MongoId)){ $id = new \MongoId($id); }

        $item = $this->collection->findOne(array('_id'=> $id), $select);

        if(is_null($item)){
            return ResponseHelper::notFound("Room type not found");
        }

        // language
        if($ctx->isAdminConsumer()){
            $item['name'] = $item['name'][$ctx->getLang()];
            $item['detail'] = $item['detail'][$ctx->getLang()];
            $item['feature'] = $item['feature'][$ctx->getLang()];
        }

        $picsRef = $item['pictures'];
        //$item['pictures'] = array();
        unset($item['pictures']);
        foreach($picsRef as $key=> $picRef){
            $img = \Main\Helper\Image::instance()->findByRef($picRef);
            $item['pictures'][] = $img->toArrayResponse();
        }
        $item['thumb'] = \Main\Helper\Image::instance()->findByRef($item['thumb'])->toArrayResponse();

        $item['id'] = $item['_id']->{'$id'};
        unset($item['_id']);

        $item['node'] = $this->makeNode($item['id']);

        return $item;
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
        $select = array("name", "detail", "feature", "price", "pictures", "thumb");
        $condition = array();
        $cursor = $this->collection
            ->find($condition, $select)
            ->limit($default['limit'])
            ->skip($skip)
            ->sort(array('seq'=> -1));

        $total = $this->collection->count($condition);
        $length = $cursor->count();

        $data = array();
        foreach($cursor as $item){
            /*
            $thumbModel = Image::load(\MongoDBRef::get($this->db, $item['thumb']));
            $item['thumb'] = $thumbModel->toArrayResponse();
            $item['node'] = array(
                "pictures"=> URL::absolute('/roomtype/pictures?roomtype_id='.$item['_id']->{'$id'})
            );
            */

            // language
            if(!$ctx->isAdminConsumer()){
                $item['name'] = $item['name'][$ctx->getLang()];
                $item['detail'] = $item['detail'][$ctx->getLang()];
                $item['feature'] = $item['feature'][$ctx->getLang()];
            }

            $picsRef = $item['pictures'];
            $item['pictures'] = array();
            unset($item['pictures']);
            foreach($picsRef as $key=> $picRef){
                $img = \Main\Helper\Image::instance()->findByRef($picRef);
                $item['pictures'][] = $img->toArrayResponse();
            }

            //$item['thumb'] = $item['pictures'][0];
            //$item['thumb'] = $item['pictures'][0];

            $item['thumb'] = \Main\Helper\Image::instance()->findByRef($item['thumb'])->toArrayResponse();

            $item['id'] = $item['_id']->{'$id'};
            unset($item['_id']);

            $item['node'] = $this->makeNode($item['id']);
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

    public function sort($param, ContextInterface $ctx = null){
        foreach($param['id'] as $key=> $id){
            $mongoId = new \MongoId($id);
            $seq = $key+$param['offset'];
            $this->collection->update(array('_id'=> $mongoId), array('$set'=> array('seq'=> $seq)));
        }
        return array('success'=> true);
    }

    public function getItemPictures($id, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $select = array("name", "detail", "feature", "price", "pictures");

        if(!($id instanceof \MongoId)){ $id = new \MongoId($id); }

        $item = $this->collection->findOne(array('_id'=> $id), $select);

        $pictures = array();
        $picsRef = $item['pictures'];
        //$item['pictures'] = array();
        unset($item['pictures']);
        foreach($picsRef as $key=> $picRef){
            $pic = \MongoDBRef::get($this->db, \MongoDBRef::create("pictures", $picRef['$id']));
            $img = Image::load($pic);
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

    public function add($params, ContextInterface $ctx = null){
        $pictures = array();
        if(isset($params['pictures'])){
            $pictures = $params['pictures'];
        }
        $params['pictures'] = array();

        $thumbModel = \Main\Helper\Image::instance()->add($params['thumb']);
        $params['thumb'] = $thumbModel->getDBRef();

        $this->collection->insert($params);

        foreach($pictures as $picture){
            $picture = \Main\Helper\Image::instance()->add($picture);
            $picDBRef = \MongoDBRef::create("pictures", $picture->getMongoId());
            $this->collection->update(array("_id"=> $params['_id']), array(
                '$push'=> array('pictures'=> $picDBRef)
            ));
        }

        $entity = $this->get($params['_id'], $ctx);
        return $entity;
    }

    public function edit($id, $params, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $set = $params;
        if(isset($set['pictures'])){
            unset($set['pictures']);
        }
        $set = ArrayHelper::ArrayGetPath($set);

        if(isset($set['thumb'])){
            $thumbModel = \Main\Helper\Image::instance()->add($set['thumb']);
            $set['thumb'] = $thumbModel->getDBRef();
        }
        //$id = $params['id'];
        if(!($id instanceof \MongoId)){ $id = new \MongoId($id); }
        $this->collection->update(array('_id'=> $id), array('$set'=> $set));

        return $this->get($id, $ctx);
    }

    public function delete($id, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        if(!($id instanceof \MongoId)){
            $id = new \MongoId($id);
        }

        $this->collection->remove(array("_id"=> $id));
        return array("success"=> true);
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

    private function makeNode($id){
        return array(
            'pictures'=> URL::absolute('/roomtype/'.$id.'/pictures'),
            'share'=> URL::share('/roomtype/'.$id)
        );
    }
}