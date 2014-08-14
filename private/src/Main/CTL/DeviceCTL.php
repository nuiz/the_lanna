<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/16/14
 * Time: 3:33 PM
 */

namespace Main\CTL;
use Main\Service\DeviceService;

/**
 * @Restful
 * @uri /device
 */
class DeviceCTL extends BaseCTL {
    /**
     * @GET
     * @uri /has
     */
    public function has(){
        return DeviceService::instance()->has($this->reqInfo->inputs());
    }
    /**
     * @POST
     */
    public function add(){
        return DeviceService::instance()->add($this->reqInfo->inputs());
    }
    /**
     * @DELETE
     */
    public function delete(){
        return DeviceService::instance()->delete($this->reqInfo->inputs());
    }
}