<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/17/14
 * Time: 3:06 PM
 */

namespace Main\Context;


class Context implements ContextInterface {
    private $lang = "en", $defaultLang = "en";

    public function getLang()
    {
        return $this->lang;
    }

    public function setLang($lang)
    {
        return $this->lang = $lang;
    }

    public function getDefaultLang()
    {
        return $this->defaultLang;
    }

    public function isDefaultLang()
    {
        return $this->defaultLang == $this->lang;
    }
}