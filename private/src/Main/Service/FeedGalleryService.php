<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/23/14
 * Time: 11:09 AM
 */

namespace Main\Service;


use Main\Context\ContextInterface;
use Main\DB;
use Main\Helper\Image;

class FeedGalleryService extends BaseService {
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
        $this->collection = $this->db->feed_gallery;
    }

    public function add($params){
        $entity = $this->getEntity();
        $pictures = array();
        foreach($params['pictures'] as $b64){
            $imgModel = Image::instance()->add($b64);
            $this->collection->update(array('_id'=> $entity['_id']), array(
                '$push'=> array('pictures'=> $imgModel->getDBRef())
            ));
            $pictures[] = $imgModel->toArrayResponse();
        }

        return $pictures;
    }

    public function gets($options = array(), ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $default = array(
            "page"=> 1,
            "limit"=> 15
        );
        $options = array_merge($default, $options);

        $data = array();
        $entity = $this->getEntity();
        foreach($entity['pictures'] as $picRef){
            $imgModel = Image::instance()->findByRef($picRef);
            $data[] = $imgModel->toArrayResponse();
        }

        $length = count($data);
        $total = $length;

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

    public function getDetail(ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $entity = $this->getEntity();
        unset($entity['pictures'], $entity['_id']);

        // if not admin get detail all language
        if(!$ctx->isAdminConsumer()){
            $entity['detail'] = $entity['detail'][$ctx->getLang()];
        }

        return $entity;
    }

    public function editDetail($params, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $entity = $this->getEntity();

        $set = array();
        if(isset($params['detail']['en'])){
            $set['detail.en'] = $params['detail']['en'];
        }
        if(isset($params['detail']['th'])){
            $set['detail.th'] = $params['detail']['th'];
        }

        $this->collection->update(array('_id'=> $entity['_id']),array('$set'=> $set));

        $entity = $this->getEntity();
        unset($entity['pictures'], $entity['_id']);
        return $this->getDetail($ctx);
    }

    public function delete($params, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $entity = $this->getEntity();
        $id = $entity['_id'];

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

    public function getEntity(){
        $entity = $this->collection->findOne();
        if(is_null($entity)){
            $entity = array("pictures"=> array(), "detail"=>
                array(
                    "en"=> "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
                    "th"=> "Lorem Ipsum คือ เนื้อหาจำลองแบบเรียบๆ ที่ใช้กันในธุรกิจงานพิมพ์หรืองานเรียงพิมพ์ มันได้กลายมาเป็นเนื้อหาจำลองมาตรฐานของธุรกิจดังกล่าวมาตั้งแต่ศตวรรษที่ 16 เมื่อเครื่องพิมพ์โนเนมเครื่องหนึ่งนำรางตัวพิมพ์มาสลับสับตำแหน่งตัวอักษรเพื่อทำหนังสือตัวอย่าง Lorem Ipsum อยู่ยงคงกระพันมาไม่ใช่แค่เพียงห้าศตวรรษ"
                )
            );
            $this->collection->insert($entity);
        }

        return $entity;
    }
}