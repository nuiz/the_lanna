<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/31/14
 * Time: 2:29 PM
 */

namespace Main\CTL;
use Main\Service\DevService;


/**
 * @Restful
 * @uri /dev
 */
class DevelopCTL {
    /**
     * @GET
     * @uri /sync/node
     */
    public function syncNode(){
        try {
            DevService::instance()->syncNode();
        }
        catch (\Exception $e){
            return array('exception'=> array(
                'name'=> get_class($e),
                'message'=> $e->getMessage(),
                'code'=> $e->getCode()
            ));
        }
        return array("success"=> true);
    }
}