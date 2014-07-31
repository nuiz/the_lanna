<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/23/14
 * Time: 10:46 AM
 */

namespace ServiceTest;


use Main\Service\FeedService;

class FeedServiceTest extends \PHPUnit_Framework_TestCase {
    public function testAdd()
    {
        $entity = FeedService::instance()->add(array(
            "name"=> "Lorem Ipsum",
            "detail"=> "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
            "thumb"=> base64_encode(file_get_contents("http://www.dknhotels.com/wp-content/themes/dknhotels/images/home-slides/dkn-hotels-latest-news-top.jpg"))
        ));
    }

    public function testGets(){
        FeedService::instance()->gets();
    }
}