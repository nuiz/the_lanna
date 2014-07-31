<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/26/14
 * Time: 3:31 PM
 */

namespace Main\CTL;

/**
 * @Restful
 * @uri /info
 */
class InfoCTL extends BaseCTL {
    /**
     * @GET
     * @uri /languages
     */
    public function getLanguages(){
        return array("en", "th");
    }
}