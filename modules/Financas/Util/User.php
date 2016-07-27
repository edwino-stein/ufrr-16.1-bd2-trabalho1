<?php
namespace Financas\Util;
use Financas\Util\Session;

class User{

    protected $sessionName;
    protected $data;

    public function __construct($sessionName){
        $this->sessionName = $sessionName;
        $this->data = $this->readSession();
    }

    public function readSession(){
        return Session::get($this->sessionName, null);
    }

    public function writeSession(){
        Session::set($this->sessionName, $this->data);
    }

    public function getData($key = null){
        if(empty($key) || !isset($this->data[$key])) return $this->data;
        return $this->data[$key];
    }

    public function isGuest(){
        return $this->data === null;
    }

    public function setData($data, $write = true){
        $this->data = $data;
        if($write) $this->writeSession();
    }

    public function clean(){
        $this->data = null;
        Session::remove($this->sessionName);
    }

    public static function init($sessionName){
        Session::init();
        return new self($sessionName);
    }
}
