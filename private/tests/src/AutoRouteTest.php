<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/15/14
 * Time: 11:34 AM
 */

use Main\AutoRoute;

class AutoRouteTest extends \PHPUnit_Framework_TestCase {
    public function testReadCTL(){
        AutoRoute::readCTL();
    }

    public function testMapCTL(){
        $route = AutoRoute::mapAllCTL();
        $getContact = $route->match("/contact", "post");
    }
}