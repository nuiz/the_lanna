<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 6/28/14
 * Time: 11:31 AM
 */

use Main\Service;

class ServiceFactory {
    static public function service($name, $version = 1){
        $class = $name.'_V'.$version;
        return new $class();
    }
}