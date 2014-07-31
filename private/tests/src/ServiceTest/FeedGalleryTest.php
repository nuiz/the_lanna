<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/23/14
 * Time: 11:56 AM
 */

namespace ServiceTest;


use Main\Service\FeedGalleryService;

class FeedGalleryTest extends \PHPUnit_Framework_TestCase {

    public function testAdd()
    {
        $pictures = array();
        $pictures[] = base64_encode(file_get_contents("http://media-cdn.tripadvisor.com/media/photo-s/03/8f/92/a5/hotel-bolivar.jpg"));
        $pictures[] = base64_encode(file_get_contents("http://media-cdn.tripadvisor.com/media/photo-s/01/72/1e/70/eser-premium-hotel-spa.jpg"));
        $pictures[] = base64_encode(file_get_contents("http://media.amoma.com/medias/hotels/images/italy/jesolo/108490-hotel_bolivar/hotelinformation_3.jpg"));
        $pictures[] = base64_encode(file_get_contents("http://ostrovok2-st.cdn.ngenix.net/t/orig/mec/hotels/5000000/4290000/4283600/4283532/4283532_4_b.jpg"));
        $items = FeedGalleryService::instance()->add(array("pictures"=> $pictures));
    }
}
 