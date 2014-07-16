<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/15/14
 * Time: 4:14 PM
 */

namespace Main\CTL;

/**
 * @Restful
 * @uri /site
 */
class SiteCTL extends BaseCTL {
    /**
     * @GET
     */
    public function index(){
        return "";
    }

    /**
     * @POST
     */
    public function post(){

    }

    /**
     * @PUT
     * @uri /[i:id]
     */
    public function put($id, $param){

    }
}