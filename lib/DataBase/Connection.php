<?php
namespace DataBase;

use DataBase\Errors;

class Connection {

    protected static $erros = array(
        0 => Errors::DRIVER_INVALID,
        2005 => Errors::CONNECTION_ERROR,
        1044 => Errors::DATA_BASE_INVALID,
        1045 => Errors::USER_OR_PASSWORD_INVALID,
    );

    protected static $instance = null;
    const DSN_TEMPLATE = '{driver}:host={host};dbname={dbname}';

    protected static function getDefaultConfig(){
        return array(
            'host' => 'localhost',
            'driver' => 'mysql',
            'dbname' => '',
            'user' => 'root',
            'password' => ''
        );
    }

    protected $pdoConnection;

    protected function __construct($config){

        try{
            $this->pdoConnection = new \PDO(
                $config['dsn'],
                $config['username'],
                $config['password'],
                array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)
            );
        }
        catch(\Exception $e){
            $code = isset(self::$erros[$e->getCode()]) ? self::$erros[$e->getCode()] : Errors::UNKNOW_ERROR;
            throw Errors::getException(self::$erros[$e->getCode()], $e);
        }
    }

    public function query($sql){
        return $this->pdoConnection->exec($sql);
    }

    public function createQuery($sql){

        try{
            $query = $this->pdoConnection->prepare($sql);
        }
        catch(\Exception $e){
            Errors::getException(Errors::CANT_CREATE_QUERY, $e);
        }

        if($query === false) Errors::getException(Errors::CANT_CREATE_QUERY, $e);
        return $query;
    }

    protected function __clone() {}

    public static function getConnection($config = null){
        if(is_array($config)) self::setConnection($config);
        return self::$instance;
    }

    public static function setConnection($config){
        if(!is_array($config)) return;
        self::$instance = new self(self::parseConfg($config));
    }

    public static function getLastInsertId(){
        return self::$instance->pdoConnection->lastInsertId();
    }

    protected static function parseConfg($config){

        $defaultConfig = self::getDefaultConfig();

        return array(
            'dsn' => str_replace(
                array('{driver}', '{host}', '{dbname}'),
                array(
                    isset($config['driver']) ? $config['driver'] : $defaultConfig['driver'],
                    isset($config['host']) ? $config['host'] : $defaultConfig['host'],
                    isset($config['dbname']) ? $config['dbname'] : $defaultConfig['dbname']
                ),
                self::DSN_TEMPLATE
            ),
            'username' => isset($config['user']) ? $config['user'] : $defaultConfig['user'],
            'password' => isset($config['password']) ? $config['password'] : $defaultConfig['password']
        );
    }

    public static function isInited(){
        return self::$instance !== null;
    }
}
