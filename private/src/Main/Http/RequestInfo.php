<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/17/14
 * Time: 1:01 PM
 */

namespace Main\Http;


class RequestInfo {
    private $inputs = array(),
        $params = array(),
        $queries = array(),
        $files = array(),
        $method = 'GET';

    public function __construct($method, $queries, $params, $files){
        $this->method = $method;
        $this->queries = $queries;
        $this->params = $params;
        $this->files = $files;

        $this->inputs = array_merge($this->queries, $this->params);
    }

    public static function loadFromGlobal()
    {
        $ctType = isset($_SERVER['CONTENT_TYPE'])? null: $_SERVER['CONTENT_TYPE'];
        $method = isset($_SERVER['REQUEST_METHOD'])? 'GET': $_SERVER['CONTENT_TYPE'];

        if($ctType=='application/json'){
            $jsonText = file_get_contents('php://input');
            $params = json_decode($jsonText, true);
        }
        else if($method=='POST'){
            $params = $_POST;
        }
        else if($method=='PUT' || $method == 'DELETE'){
            $put = array();
            parse_str(file_get_contents("php://input"), $put);
            $params = $put;
        }
        else {
            $params = $_GET;
        }

        return new self($method, $_GET, $params, $_FILES);
    }

    public function params()
    {
        return $this->params;
    }

    public function param($name, $default = null){
        return isset($this->params[$name])? $this->params[$name]: $default;
    }

    public function hasParam($name){
        return isset($this->params[$name]);
    }

    public function inputs()
    {
        return $this->inputs;
    }

    public function input($name, $default = null)
    {
        return isset($this->inputs[$name])? $this->params[$name]: $default;
    }

    public function hasInput($name){
        return isset($this->inputs[$name]);
    }

    public function getMethod()
    {
        return $this->method;
    }
}