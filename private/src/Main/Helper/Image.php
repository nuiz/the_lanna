<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/19/14
 * Time: 11:40 AM
 */

namespace Main\Helper;


use Exception\Image\InvalidImageResourceException;
use Main\DB;

class Image {
    private $collection, $dir = '/../../../pictures/';
    private static $instance;

    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct(){
        $this->dir = dirname(__FILE__).$this->dir;
        $db = DB::getDB();
        $this->collection = $db->pictures;
    }

    public function _getimagesizefromstring($data)
    {
        $uri = 'data://application/octet-stream;base64,'.base64_encode($data);
        return @getimagesize($uri);
    }

    public function checkBase64Image($b64) {
        $data = base64_decode($b64);
        if (!function_exists('getimagesizefromstring')) {
            $info = $this->_getimagesizefromstring($data);
        }
        else {
            $info = @getimagesizefromstring($data);
        }

        if(!$info){
            return false;
        }

        if ($info[0] > 0 && $info[1] > 0 && isset($info['mime'])) {
            return true;
        }

        return false;
    }

    public function add($input){
        if($this->checkBase64Image($input)){
            $resource = imagecreatefromstring(base64_decode($input));
        }
        else if(is_file($input)){
            $resource = imagecreatefromstring(file_get_contents($input));
        }
        else if($this->_getimagesizefromstring($input)) {
            $resource = imagecreatefromstring($input);
        }
        else {
            return false;
        }

        if(get_resource_type($resource) != 'gd')
            throw new InvalidImageResourceException();

        $w = imagesx($resource);
        $h = imagesy($resource);

        $ratio = $h/$w;
        $ratio2 = 1136/640;

        if($h <= 1136 || $w <= 640){
            $h2 = $h;
            $w2 = $w;
        }
        else if($ratio > $ratio2){
            $h2 = 1136;
            $w2 = 1136/$ratio;
        }
        else {
            $w2 = 640;
            $h2 = 640*$ratio;
        }

        $path = md5(time().microtime().rand(1,100)).".jpg";
        $collection = DB::getDB()->pictures;

        // resize save
        $image_p = imagecreatetruecolor($w2, $h2);
        imagecopyresampled($image_p, $resource, 0, 0, 0, 0, $w2, $h2, $w, $h);
        imagejpeg($image_p, $this->dir.$path, 100);

        imagedestroy($image_p);
        imagedestroy($resource);

        $entity = array(
            "path"=> $path, "width"=> $w2, "height"=> $h2
        );

        $collection->insert($entity);
        $entity = \Main\DataModel\Image::load($entity);

        return $entity;
    }

    public function findOne($_id){
        if(!($_id instanceof \MongoId)){
            $_id = new \MongoId($_id);
        }
        return \Main\DataModel\Image::findOne($_id);
    }

    public function findByRef($ref){
        return $this->findOne($ref['$id']);
    }
}