<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 5:11 PM
 */

namespace Main\CTL;
use Main\Service\ServiceService;

/**
 * @Restful
 * @uri /service
 */
class ServiceCTL extends BaseCTL {
    /**
     * @GET
     * @uri /[h:id]/pictures
     */
    public function getPictures(){
        $items = ServiceService::instance()->getItemPictures($this->reqInfo->urlParam('id'));
        return $items;
    }
}