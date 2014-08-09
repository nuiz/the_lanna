<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 8/5/14
 * Time: 11:18 AM
 */

namespace Main\CTL;
use Main\Service\TimeService;

/**
 * @Restful
 * @uri /timeupdate
 */
class TimeCTL extends BaseCTL {
    /**
     * @GET
     */
    public function get(){
        $entity = TimeService::instance()->get();
        unset($entity['_id']);
        return $entity;
    }
}