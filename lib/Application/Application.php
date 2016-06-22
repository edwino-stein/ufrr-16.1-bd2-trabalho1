<?php
namespace Application;

include_once('autoload.php');

use Application\AbstractController;
use Application\HttpRequest;
use Application\View;
use DataBase\Connection;

abstract class Application {

    protected $config = array();
    protected $request;

    private static $instance;
    private static $moduleName;

    private $controllerName;
    private $actionName;

    private static $namespaces = '/namespaces.php';
    private static $dirs = array(
        'public' => '/public',
        'lib' => '/lib',
        'modules' => '/modules'
    );

    protected function init(){

        $default = include(self::getDir('lib').'/Application/defaultConfig.php');
        self::apply($this->config, $default);

        if(!empty($this->config['db']) && is_array($this->config['db']))
            Connection::setConnection($this->config['db']);

        $this->request = new HttpRequest();
        $route = $this->request->getRoute();

        if($route == '/'){
            $this->controllerName = $this->config['defaultController'];
            $this->actionName = null;
        }
        elseif(preg_match('/^[\/]([a-zA-z])([a-zA-z]|\d)*(\/([a-zA-z])([a-zA-z]|\d)*)?[\/]?$/m', $route) === 1){
            $route = explode('/', $route);
            $this->controllerName = $route[1];
            $this->actionName = isset($route[2]) ? $route[2] : null;
        }

        $controller = $this->getControllerInstance($this->controllerName);
        $result =  $controller->callAction($this->actionName);

        if(is_string($result) || $result instanceof View){
            echo $result;
        }
    }

    protected function getControllerInstance($name){

        $path = self::getDir('modules').'/'.self::$moduleName.'/Controller/';
        $controller = null;
        $upperName = strtoupper($name);

        if(!is_dir($path) || !($dh = opendir($path)))
            throw new \Exception("Diretório de controller é inválido.", 1);

        while (($file = readdir($dh))){
            if($file === '.' || $file === '..') continue;

            $file = explode('.php', $file)[0];

            if($upperName === strtoupper($file)){
                $controller = self::$moduleName.'\\Controller\\'.$file;
                break;
            }

        }
        closedir($dh);

        if($controller === null)
            throw new \Exception('Nenhum controller encontrado.', 1);

        $instance = new $controller();
        $instance->controllerName = $name;
        return $instance;
    }

    public function config(){
        return $this->config;
    }

    public function request(){
        return $this->request;
    }

    public function db(){
        return Connection::getConnection();
    }

    public function getControlerName(){
        return $this->controllerName;
    }

    public function getActionName(){
        return $this->actionName;
    }

    public static function run($module){

        self::initDir();
        self::initNamespaces();

        if(!isset(self::$namespaces[$module]))
            throw new \Exception("O modulo \"$module\" é inválido.", 1);

        self::$instance = $module.'\\'.$module;
        self::$instance = new self::$instance();

        if(!(self::$instance instanceof Application))
            throw new \Exception("O modulo \"$module\" é inválido.", 1);

        self::$moduleName = $module;
        self::$dirs['moduleLoaded'] = self::getDir('modules').'/'.$module;
        self::$instance->init();
    }

    public static function app(){
        return self::$instance;
    }

    private static function initNamespaces(){

        self::$namespaces = include(self::getDir('lib').self::$namespaces);
        $root = self::getDir();

        foreach (self::$namespaces as $key => $value)
            self::$namespaces[$key] = $root.'/'.$value;

        $modulesDir = $modulesDir = self::getDir('modules');
        if(!is_dir($modulesDir) || !($dh = opendir($modulesDir))) return;

        while (($file = readdir($dh))){

            if($file === '.' || $file === '..') continue;
            if(!is_dir($modulesDir.'/'.$file)) continue;
            if(!file_exists($modulesDir.'/'.$file.'/'.$file.'.php')) continue;
            if(isset(self::$namespaces[$file])) continue;

            self::$namespaces[$file] = $modulesDir.'/'.$file;
        }
        closedir($dh);
    }

    private static function initDir(){

        chdir(dirname(dirname(__DIR__)));
        $root = getcwd();
        foreach (self::$dirs as $key => $value)
            self::$dirs[$key] = $root.$value;

        self::$dirs['root'] = $root;
    }

    public static function getDir($dir = null){
        if(is_string($dir) && isset(self::$dirs[$dir])) return self::$dirs[$dir];
        return self::$dirs['root'];
    }

    public static function autoLoad($className){

        $namespace = explode('\\', $className);
        $base = array_shift($namespace);

        if(empty($namespace)) return;
        if(!isset(self::$namespaces[$base]))
            throw new \Exception('O Namespace "'.$base.'\\'.implode('\\', $namespace).'" não foi registrado.', 1);

        require_once(self::$namespaces[$base].'/'.implode('/', $namespace).'.php');
    }

    public static function apply(&$destination, $default){

        foreach ($default as $key => $value) {

            if(!isset($destination[$key])){
                $destination[$key] = $value;
            }
            else if(is_array($value)){
                self::apply($destination[$key], $value);
            }
        }
    }
}
