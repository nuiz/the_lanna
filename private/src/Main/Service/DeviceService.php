<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 8/13/14
 * Time: 3:39 PM
 */

namespace Main\Service;


use Main\Context\ContextInterface;
use Main\DB;
use Main\Helper\ResponseHelper;

class DeviceService extends BaseService {
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
        $collection = $db->devices;
        $this->collection = $collection;
    }

    /*
    public function add($params, ContextInterface $ctx = null){
        $allowed = array('type', 'key');
        $params = array_intersect_key($params, array_flip($allowed));

        if(empty($params['type'])){
            return ResponseHelper::error('Require parameter type');
        }
        if(empty($params['key'])){
            return ResponseHelper::error('Require parameter key');
        }

        $has = $this->has($params);
        if($has['has']){
            return ResponseHelper::error('This key and device has stored');
        }

        $this->collection->insert($params);

        return $params;
    }

    public function has($params, ContextInterface $ctx = null){
        $allowed = array('type', 'key');
        $params = array_intersect_key($params, array_flip($allowed));

        if(empty($params['type'])){
            return ResponseHelper::error('Require parameter type');
        }
        if(empty($params['key'])){
            return ResponseHelper::error('Require parameter key');
        }

        $entity = $this->collection->findOne($params);
        return array('has'=> !is_null($entity));
    }

    public function delete($params, ContextInterface $ctx = null){
        $allowed = array('type', 'key');
        $params = array_intersect_key($params, array_flip($allowed));

        if(empty($params['type'])){
            return ResponseHelper::error('Require parameter type');
        }
        if(empty($params['key'])){
            return ResponseHelper::error('Require parameter key');
        }

        $this->collection->remove($params);

        return array('success'=> true);
    }
    */

    public function edit($params, ContextInterface $ctx = null){
        $allowed = array('type', 'key', 'admit');
        $params = array_intersect_key($params, array_flip($allowed));

        if(!isset($params['type'])){
            return ResponseHelper::error('Require parameter type');
        }
        if(!isset($params['key'])){
            return ResponseHelper::error('Require parameter key');
        }
        if(!isset($params['admit'])){
            return ResponseHelper::error('Require parameter admit');
        }

        $entity = $this->get($params, $ctx);
        $condition = $params;
        unset($condition['admit']);
        $this->collection->update($condition, array('$set'=>array('admit'=> (bool)$params['admit'])));

        $entity = $this->get($params);
        return $entity;
    }

    public function get($params, ContextInterface $ctx = null){
        $allowed = array('type', 'key');
        $condition = array_intersect_key($params, array_flip($allowed));
        $entity = $this->collection->findOne($condition);
        if(is_null($entity)){
            $entity = $params;
            $entity['admit'] = true;
            $this->collection->insert($entity);
        }
        return $entity;
    }
}