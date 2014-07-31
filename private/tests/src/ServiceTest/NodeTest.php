<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 3:11 PM
 */

namespace ServiceTest;


use Main\Service\NodeService;

class NodeTest extends \PHPUnit_Framework_TestCase {
    public function testGets(){
        $data = NodeService::instance()->gets();
    }
}