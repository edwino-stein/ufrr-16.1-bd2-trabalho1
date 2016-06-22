<?php
namespace DataBase;

class DataBaseException extends \Exception{

    protected $prev = null;

    public function __construct($code, $message, \Exception $prev = null){
        $this->code = $code;
        $this->message = $message;
        $this->prev = $prev;
    }

    public function getPrevException(){
        return $this->prev;
    }

}
