<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 11:45 AM
 */

namespace ServiceTest;


use Main\Service\FolderService;

class FolderTest extends \PHPUnit_Framework_TestCase {
    public function testAdd(){
        $folder = FolderService::instance()->add(array(
            'name'=> 'Thai massage',
            'thumb'=> base64_encode(file_get_contents('media/1.jpg'))
        ));

        $this->assertArrayHasKey('name', $folder);
        $this->assertArrayHasKey('thumb', $folder);
        $this->assertArrayHasKey('parent', $folder);

        // has parent

        $folder = FolderService::instance()->add(array(
            'name'=> 'Test folder',
            'thumb'=> base64_encode(file_get_contents('media/1.jpg')),
            'parent_id'=> $folder['_id']
        ));

        $this->assertArrayHasKey('name', $folder);
        $this->assertArrayHasKey('thumb', $folder);
        $this->assertNotEmpty('parent', $folder['parent']);

        // test get has parent
        $folder = FolderService::instance()->get($folder['_id']);

        $this->assertArrayHasKey('name', $folder);
        $this->assertArrayHasKey('thumb', $folder);
        $this->assertNotEmpty('parent', $folder['parent']);
    }
}