<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/16/14
 * Time: 3:33 PM
 */

namespace Main\CTL;
use Main\Service\ContactService;

/**
 * @Restful
 * @uri /contact
 */
class ContactCTL extends BaseCTL {
    /**
     * @GET
     */
    public function get(){
        $contact = ContactService::instance()->get();
        return $contact;
    }
    /**
     * @POST
     */
    public function edit($params){
        $contact = ContactService::instance()->edit($params);
        return $contact;
    }
}