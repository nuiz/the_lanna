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
     */
    public function get(){
        return DeviceService::instance()->get($this->reqInfo->inputs());
    }
    /**
     * @PUT
     */
    public function edit(){
        return DeviceService::instance()->edit($this->reqInfo->inputs());
    }
}