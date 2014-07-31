<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/18/14
 * Time: 4:16 PM
 */

namespace ServiceTest;


use Main\Service\ContactCommentService;

class ContactCommentTest extends \PHPUnit_Framework_TestCase {

    public function __construct(){
        $this->service = ContactCommentService::instance();
    }

    public function testAdd(){
        $response = $this->service->add(array(
            'message'=> 'add for test'
        ));

        $this->assertArrayHasKey('_id', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('created_at', $response);
        $this->assertArrayHasKey('updated_at', $response);
    }
} 