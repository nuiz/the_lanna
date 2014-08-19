<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/19/14
 * Time: 4:54 PM
 */

namespace Main\CTL;
use Main\Service\NotifyService;

/**
 * @Restful
 * @uri /notify
 */
class NotifyCTL extends BaseCTL {
    /**
     * @GET
     */
    public function gets(){
        $items = NotifyService::instance()->gets($this->reqInfo->params(), $this->getCtx());
        return $items;
    }

    /**
     * @POST
     * @PUT
     * @GET
     * @uri /opened/[h:id]
     */
    public function opened(){
        $items = NotifyService::instance()->opened($this->reqInfo->urlParam('id'), $this->getCtx());
        return $items;
    }

    /**
     * @GET
     * @uri /unopened
     */
    public function getUnopened(){
        return NotifyService::instance()->unopenedCount($this->reqInfo->params(), $this->getCtx());
    }
}