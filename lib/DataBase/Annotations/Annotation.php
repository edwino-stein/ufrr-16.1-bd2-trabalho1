<?php
namespace DataBase\Annotations;

class Annotation implements \IteratorAggregate, \ArrayAccess, \Countable{

    protected $container;

    function __construct($data){
        $this->container = is_array($data) ? $data : array();
    }

    public function hasTag($tagName){
        return isset($this->container[$tagName]);
    }

    public function getTagValue($tagName){
        return $this->hasTag($tagName) ? $this->container[$tagName] : null;
    }

    /*
        Metodos implementados de IteratorAggregate
     */

    public function getIterator() {
        return new ArrayIterator($this->container);
    }

    /*
        Metodos implementados de ArrayAccess
     */
    public function offsetSet($offset, $value) {}

    public function offsetExists($offset) {
        return $this->hasTag($offset);
    }

    public function offsetUnset($offset) {}

    public function offsetGet($offset) {
        return $this->getTagValue($offset);
    }

    /*
        Metodos implementados de Countable
     */
    public function count(){
        return count($this->container);
    }
}
