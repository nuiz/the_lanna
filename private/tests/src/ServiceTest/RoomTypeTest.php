<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/19/14
 * Time: 11:36 AM
 */

namespace ServiceTest;


use Main\Service\RoomTypeService;

class RoomTypeTest extends \PHPUnit_Framework_TestCase {
    public function testAdd(){
        $b64_image = base64_encode(file_get_contents('media/1.jpg'));
        $entity = RoomTypeService::instance()->add(array(
            'name'=> 'test ja',
            'detail'=> 'test detail ja',
            'feature'=> 'test future ja',
            'price'=> 2200,
            'pictures'=> array($b64_image, $b64_image, $b64_image)));

        $this->assertArrayHasKey('name', $entity);
        $this->assertArrayHasKey('detail', $entity);
        $this->assertArrayHasKey('feature', $entity);
        $this->assertArrayHasKey('price', $entity);
        $this->assertArrayHasKey('pictures', $entity);
    }

    public function testGets(){
        $arr = RoomTypeService::instance()->gets();
        //var_dump($arr);
    }
}