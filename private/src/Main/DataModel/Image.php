<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/19/14
 * Time: 2:49 PM
 */

namespace Main\DataModel;


use Main\DB;
use Main\Helper\URL;

class Image {
    protected $_id, $path, $width, $height;
    protected function __construct(){
    }

    public function toArrayResponse(){
        $data = get_object_vars($this);
        $data['id'] = $data['_id']->{'$id'};
        $data['url'] = URL::absolute('/picture/'.$data['id']);
        unset($data['_id']);
        return $data;
    }

    /** @return \MongoId */
    public function getMongoId(){
        return $this->_id;
    }

    public function getDBRef(){
        return \MongoDBRef::create('pictures', $this->_id);
    }

    public function getPath(){
        return $this->path;
    }

    public static function findOne($_id){
        if(!($_id instanceof \MongoId)){ $_id = new \MongoId($_id); }

        $collection = DB::getDB()->pictures;
        $data = $collection->findOne(array("_id"=> $_id));
        if(is_null($data))
            return null;

        return self::load($data);
    }

    public static function load($params){
        $model = new Image();
        $model->_id = $params['_id'];
        $model->path = $params['path'];
        $model->width = $params['width'];
        $model->height = $params['height'];
        return $model;
    }
}