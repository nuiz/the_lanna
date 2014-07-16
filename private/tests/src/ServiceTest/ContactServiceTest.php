<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/16/14
 * Time: 4:18 PM
 */

namespace ServiceTest;


use Main\Service\ContactService;

class ContactServiceTest extends \PHPUnit_Framework_TestCase {
    public function testGet(){
        $entity = ContactService::instance()->get();
        $this->assertArrayHasKey("phone", $entity);
        $this->assertArrayHasKey("website", $entity);
        $this->assertArrayHasKey("email", $entity);
        $this->assertArrayHasKey("location", $entity);
    }

    public function testEdit(){
        ContactService::instance()->edit(array(
            "phone"=> "089-999-9999"
        ));

        ContactService::instance()->edit(array(
            "email"=> "example2@example2.com"
        ));

        ContactService::instance()->edit(array(
            "website"=> "example2.com"
        ));

        $entity = ContactService::instance()->edit(array(
            "location"=> array(
                "lat"=> "18.795095000",
                "lng"=> "98.993213000"
            )
        ));
    }
}