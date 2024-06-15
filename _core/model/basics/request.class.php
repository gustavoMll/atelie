<?php

class Request {
    private $_params;
    private $_queries;
    private $_posts;
    private $_body;
    private $_parts;
    
    public function  __construct($param) {
        $params = explode("/", $param);
        $this->_params['module'] = (isset($params[0])) ? Security::antiInjection($params[0]) : '';
        $this->_params['action'] = (isset($params[1])) ? Security::antiInjection($params[1]) : '';

        for($i = 2; $i < count($params); $i+=2){
            $this->_params[$params[$i]] = (isset($params[$i+1])) ? Security::antiInjection($params[$i+1]) : '';
        }

        $this->_queries = Security::clearVars($_GET);
        $this->_posts = Security::clearVars($_POST); 
        $this->_body = file_get_contents('php://input');
        $this->_parts = $params;
    }
    
    public function getParameter($key, $defaultValue=''){
        return $this->get($key, $defaultValue);
    }
    
    public function get($key, $defaultValue=''){
        return isset($this->_params[$key]) && $this->_params[$key] != '' ? $this->_params[$key] : $defaultValue;
    }

    public function query($key, $defaultValue=''){
        return isset($this->_queries[$key]) && $this->_queries[$key] != ''  ? $this->_queries[$key] : $defaultValue;
    }

    public function getInt($key, $defaultValue=0){
        return (isset($this->_params[$key]) ? (int) $this->_params[$key] : $defaultValue);
    }

    public function getQuery(){
        return $this->_queries;
    }

    public function post($key, $defaultValue=''){
        return isset($this->_posts[$key]) && $this->_posts[$key] != ''  ? $this->_posts[$key] : $defaultValue;
    }

    public function getPost(){
        return $this->_posts;
    }

    public function body(){
        return $this->_body;
    }

    public function getIndex($key, $defaultValue=''){
        return (isset($this->_parts[$key]) ? $this->_parts[$key] : $defaultValue);
    }
}
