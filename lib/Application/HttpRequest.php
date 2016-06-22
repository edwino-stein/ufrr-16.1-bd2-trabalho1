<?php
namespace Application;

class HttpRequest {

    protected $scriptName;
    protected $baseUri;
    protected $route;

    public function __construct(){
        $this->scriptName = basename(self::getProperty('SCRIPT_NAME'));
        $url = explode($this->scriptName, self::getProperty('REQUEST_URI'));
        $this->baseUri = $url[0];
        $this->route = !isset($url[1]) || preg_match('/^[\?]/m', $url[1]) == 1 || empty($url[1]) ? '/' : explode('?', $url[1])[0];
    }

    public function getScriptName(){
        return $this->scriptName;
    }

    public function getBaseUri(){
        return $this->baseUri;
    }

    public function getRoute(){
        return $this->route;
    }

    public static function getProperty($key){
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    public function hasPost($key){
        return isset($_POST[$key]);
    }

    public function getPost($key = null, $default = null){
        if($key == null) return $_POST;
        return $this->hasPost($key) ? $_POST[$key] : $default;
    }

    public function hasQuery($key){
        return isset($_GET[$key]);
    }

    public function getQuery($key = null, $default = null){
        if($key == null) return $_GET;
        return $this->hasQuery($key) ? $_GET[$key] : $default;
    }
}
