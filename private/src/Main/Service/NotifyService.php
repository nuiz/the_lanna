<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 8/13/14
 * Time: 5:12 PM
 */

namespace Main\Service;


use Main\Context\ContextInterface;
use Main\DB;

class NotifyService extends BaseService {
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
        $collection = $db->notify;
        $this->collection = $collection;
    }

    public function gets($params, ContextInterface $ctx = null){
        if(is_null($ctx))
            $ctx = $this->getCtx();

        $default = array(
            "page"=> 1,
            "limit"=> 15
        );
        $options = array_merge($default, $params);

        $allowed = array('key', 'type');
        $condition = array('device'=> array_intersect_key($options, array_flip($allowed)));

        $skip = ($options['page']-1)*$options['limit'];
        $select = ["preview_content", "preview_header", "object", "opened", "created_at"];

        $cursor = $this->collection
            ->find($condition, $select)
            ->limit($options['limit'])
            ->skip($skip)
            ->sort(array('created_at'=> -1));

        $total = $this->collection->count($condition);
        $length = $cursor->count(true);

        $data = array();
        foreach($cursor as $item){
            if(!$ctx->isAdminConsumer()){
                $item['preview_content'] = $item['preview_content'][$ctx->getLang()];
                $item['preview_header'] = $item['preview_header'][$ctx->getLang()];
            }
            $item['object']['id'] = $item['object']['_id']->{'$id'};
            unset($item['object']['_id']);
            $item['id'] = $item['_id']->{'$id'};
            unset($item['_id']);

            $item['created_at'] = date('Y-m-d H:i:s', $item['created_at']->sec);
            $data[] = $item;
        }

        return [
            'length'=> $length,
            'total'=> $total,
            'data'=> $data,
            'paging'=> [
                'page'=> (int)$options['page'],
                'limit'=> (int)$options['limit']
            ]
        ];
    }

    public function unopenedCount($params, ContextInterface $ctx = null){
        $item = DeviceService::instance()->get($params);
        if(!isset($item['display_notify_number'])){
            return $item;
        }
        return ['length'=> $item['display_notify_number']];
    }

    public function opened($id, ContextInterface $ctx = null){
        if(!($id instanceof \MongoId)){
            $id = new \MongoId($id);
        }
        $criteria = array('_id'=> $id);
        $this->collection->update($criteria, array('$set'=> array('opened'=> true)));

        return array('success'=> true);
    }
}