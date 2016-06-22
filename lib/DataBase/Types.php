<?php
namespace DataBase;

abstract class Types{

    const NULL_TYPE = 'NULL';

    public static function casting($value, $type){

        switch (strtolower($type)) {

            case 'int':
            case 'integer':
                return self::toInt($value);
            break;

            case 'float':
                return self::toFloat($value);
            break;

            case 'double':
                return self::toDouble($value);
            break;

            case 'bool':
            case 'boolean':
                return self::toBool($value);
            break;

            case 'string':
                return self::toString($value);
            break;

            case 'datetime':
                return self::toDate($value);
            break;
        }

        return $value;
    }

    public static function prepareToQuery($value, $type){

        if($value === null) return self::NULL_TYPE;

        switch (strtolower($type)) {

            case 'bool':
            case 'boolean':
                $value = self::toInt($value);

            case 'int':
            case 'integer':
            case 'float':
            case 'double':
                return self::toString($value);
            break;


            case 'datetime':
            case 'string':
                return "'".self::toString($value)."'";
            break;
        }

        return self::NULL_TYPE;
    }

    public static function toInt($value){
        return (int) $value;
    }

    public static function toFloat($value){
        return (float) $value;
    }

    public static function toDouble($value){
        return (double) $value;
    }

    public static function toBool($value){

        if(is_bool($value)) return $value;

        else if(is_numeric($value))
            return !(self::toInt($value) === 0);

        else if(is_string($value)){
            return strtolower($value) === 'true';
        }
    }

    public static function toString($value){

        if(is_object($value) && method_exists($value, 'toString')){
            return $value->toString();
        }

        else if($value instanceof \DateTime){
            return $value->format('Y-m-d H:i:s');
        }

        else if(is_bool($value)){
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }


    public static function toDate($value){

        if($value instanceof \DateTime) return $value;

        else if(is_string($value)){

            try {
                $value = new \DateTime($value);
            }
            catch (\Exception $e) {
                return null;
            }

            return $value;
        }

        return null;
    }

}
