<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/16/14
 * Time: 3:58 PM
 */

namespace Main\Service;


use Main\Context\Context;
use Main\Context\ContextInterface;
use Main\DB;

class ContactCommentService extends BaseService {
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
        $this->collection = $db->contacts_comments;
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
        $condition = array();
        $cursor = $this->collection
            ->find($condition)
            ->sort(array('_id'=> 1))
            ->limit($default['limit'])
            ->skip($skip);

        $total = $this->collection->count($condition);
        $length = $cursor->count();

        $data = array();
        foreach($cursor as $item){
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

    public function add($params, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $now = new \MongoDate();
        $entity = array(
            "message"=> $params['message'],
            "created_at"=> $now,
            "updated_at"=> $now
        );

        $this->collection->insert($entity);

        return $entity;
    }
}