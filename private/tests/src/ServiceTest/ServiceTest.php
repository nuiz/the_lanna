<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 11:45 AM
 */

namespace ServiceTest;


use Main\Service\ServiceService;

class ServiceTest extends \PHPUnit_Framework_TestCase {
    public function testAddRoom(){
        $entity = ServiceService::instance()->addRoom(array(
            'name'=> 'Meeting room',
            //'parent_id'=> '53ce11268ecb89f01900002a',
            'detail'=> "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
            'thumb'=> base64_encode(file_get_contents('http://www.regus.co.th/images/208-81941.jpg'))
        ));
    }

    public function testAddFood(){
        $entity = ServiceService::instance()->addFood(array(
            'name'=> 'Steak',
            'parent_id'=> '53ce2555794823100300002a',
            'detail'=> "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
            'thumb'=> base64_encode(file_get_contents('http://thumb9.shutterstock.com/display_pic_with_logo/376831/108362702/stock-photo-grilled-beef-steak-108362702.jpg')),
            'price'=> 120
        ));

        $entity = ServiceService::instance()->addFood(array(
            'name'=> 'Pasta',
            'thumb'=> base64_encode(file_get_contents('http://www.daringgourmet.com/wp-content/uploads/2013/05/Red-Pesto-Pasta-2-sm-edited-2.jpg')),
            'detail'=> "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
            'parent_id'=> '53ce2555794823100300002a',
            'price'=> 80
        ));
    }
}