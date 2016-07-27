<?php
namespace Financas\Util;

class ValidateException extends \Exception{

    protected $property;
    protected $message;

    public function __construct($property, $message){
        $this->property = $property;
        $this->message = $message;
    }

    public function getProperty(){
        return $this->property;
    }
}
