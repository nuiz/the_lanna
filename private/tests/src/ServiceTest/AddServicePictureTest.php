<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 5:03 PM
 */

namespace ServiceTest;


use Main\Service\ServiceService;

class AddServicePictureTest extends \PHPUnit_Framework_TestCase {
    public function testAddPicture(){
        $b64_a = base64_encode(file_get_contents('http://ed.files-media.com/ud/comicimg/1/128/383104/DSC_R_0252-Cover-620x392.jpg'));
        $b64_b = base64_encode(file_get_contents('http://affotd.files.wordpress.com/2011/01/image004-330215549_std.jpg'));
        $b64_c = base64_encode(file_get_contents('http://honestcooking.com/wp-content/uploads/2014/05/grill-marks.png'));
        $b64_d = base64_encode(file_get_contents('http://blog.triplepointpr.com/wp-content/uploads/2013/10/grilling-steak.jpg'));
        $b64_e = base64_encode(file_get_contents('http://drosengarten.com/wp-content/uploads/2012/06/steak-grill1.jpg'));

        ServiceService::instance()->addPictures('53ce3288794823840f00002a', array($b64_a, $b64_b, $b64_c, $b64_d, $b64_e));
    }
}