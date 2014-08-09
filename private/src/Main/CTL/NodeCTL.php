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
        $options = $this->reqInfo->params();
        $items = NodeService::instance()->gets($options, $this->getCtx());
        return $items;
    }

    /**
     * @GET
     * @uri /[h:id]/children
     */
    public function children(){
        $params = array('parent_id'=> $this->reqInfo->urlParam('id'));
        $options = $this->reqInfo->params();
        $options['params'] = $params;
        $items = NodeService::instance()->gets($options, $this->getCtx());
        return $items;
    }

    /**
     * @POST
     * @uri /sort
     */
    public function sort(){
        $res = NodeService::instance()->sort($this->reqInfo->params(), $this->getCtx());
        return $res;
    }
}