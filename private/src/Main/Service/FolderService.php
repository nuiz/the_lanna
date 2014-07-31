<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 11:49 AM
 */

namespace Main\Service;


use Main\Context\ContextInterface;
use Main\DB;
use Main\Helper\ArrayHelper;
use Main\Helper\Image;
use Main\Helper\ResponseHelper;

class FolderService extends BaseService {
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
        $this->collection = $this->db->folders;
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

        $entity['id'] = $entity['_id']->{'$id'};
        unset($entity['_id']);
        return $entity;
    }

    public function add(array $params, ContextInterface $ctx = null){
        $entity = $params;
        $thumb = Image::instance()->add($params['thumb']);
        $entity['thumb'] = $thumb->getDBRef();

        $entity['parent'] = 0;
        if(!empty($params['parent_id'])){
            $parent = $this->get($params['parent_id']);
            $parentRef = \MongoDBRef::create("folders", $parent['_id']);
            $entity['parent'] = $parentRef;
            unset($entity['parent_id']);
        }
        $this->collection->insert($entity);

        // Node service update
        NodeService::instance()->addOrEdit($entity, 'folder');

        return $this->get($entity['_id']);
    }

    public function edit($_id, array $params, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        if(!($_id instanceof \MongoId)){
            $_id = new \MongoId($_id);
        }

        $entity = $params;

        if(isset($params['parent_id'])){
            $parent = $this->get($params['parent_id']);
            if(is_null($parent)){
                throw new \Exception("Not found parent_id ".$params['parent_id']);
            }
            $parentRef = \MongoDBRef::create("folders", $parent['_id']);

            $entity['parent'] = $parentRef;
            unset($entity['parent_id']);
        }
        if(isset($params['thumb'])){
            $thumb = Image::instance()->add($params['thumb']);
            $entity['thumb'] = $thumb->getDBRef();
        }

        $entity = ArrayHelper::ArrayGetPath($entity);

        $this->collection->update(array('_id'=> $_id), array('$set'=> $entity));

        $entity = $this->db->folders->findOne(array('id'=> $_id));
        $entity['_id'] = $_id;
        unset($entity['id']);
        // Node service update
        NodeService::instance()->addOrEdit($entity, 'folder', $ctx);

        return $this->get($_id);
    }
}