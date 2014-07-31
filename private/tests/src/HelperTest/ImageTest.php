<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/19/14
 * Time: 11:50 AM
 */

namespace HelperTest;


use Main\Helper\Image;

class ImageTest extends \PHPUnit_Framework_TestCase {
    public function testCheckB64(){
        $imageBase64 = base64_encode(file_get_contents('media/1.jpg'));
        $stringBase64 = base64_encode("test na ja");
        $this->assertEquals(true, Image::instance()->checkBase64Image($imageBase64));
        $this->assertEquals(false, Image::instance()->checkBase64Image($stringBase64));
    }

    public function testAdd(){
        $imageBase64 = base64_encode(file_get_contents('media/2.jpg'));
        $imgModel = Image::instance()->add($imageBase64);
        $this->assertInstanceOf('Main\DataModel\Image', $imgModel);
    }

    public function testFind(){
        $imgModel = Image::instance()->findOne(new \MongoId("53ca36478ecb894c02000029"));
        $this->assertInstanceOf('Main\DataModel\Image', $imgModel);
        $arr = $imgModel->toArrayResponse();
        $this->assertArrayHasKey("_id", $arr);
        $this->assertArrayHasKey("path", $arr);
        $this->assertArrayHasKey("width", $arr);
        $this->assertArrayHasKey("height", $arr);
        $this->assertArrayHasKey("url", $arr);
    }
}