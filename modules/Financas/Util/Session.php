<?php
namespace Financas\Util;

abstract class Session {

    protected static $inited = false;

    public static function set($key, $data){
        if(self::$inited) $_SESSION[$key] = $data;
    }

    public static function has($key, $isEmpty = false){
        if(!self::$inited) return false;
        return $isEmpty ? isset($_SESSION[$key]) && !empty($_SESSION[$key]) : isset($_SESSION[$key]);
    }

    public static function get($key, $default = null){
        return self::has($key, true) ? $_SESSION[$key] : $default;
    }

    public static function unset($key){
        if(self::has($key)) unset($_SESSION[$key]);
    }

    public static function init(){
        if(!self::$inited){
            session_start();
            self::$inited = true;
        }
    }

    public static function destroy(){
        if(self::$inited){
            session_destroy();
            self::$inited = false;
        }
    }

    public static function clean(){
        if(self::$inited) session_unset();
    }

}
