<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 3:27 PM
 */

namespace Main\CTL;
use Main\Service\NodeService;


/**
 * @Restful
 * @uri /node
 */
class NodeCTL extends BaseCTL {
    /**
     * @GET
     */
    public function index(){
        $items = NodeService::instance()->gets();
        return $items;
    }

    /**
     * @GET
     * @uri /[h:id]/children
     */
    public function children(){
        $params = array('parent_id'=> $this->reqInfo->urlParam('id'));
        $items = NodeService::instance()->gets(array('params'=> $params));
        return $items;
    }
}