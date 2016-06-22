<?php
namespace Application;
use Application\Application;

class View {

    protected $layoutPath;

    public function __construct(array $variables, $layoutPath = null){
        $this->setVariables($variables);
        if($layoutPath !== null) $this->setLayout($layoutPath);
    }

    public function app(){
        return Application::app();
    }

    public function setLayout($path){
        $this->layoutPath = $path;
    }

    public function getLayout(){
        return $this->layoutPath;
    }

    public function setVariable($key, $value){
        $this->$key = $value;
    }

    public function setVariables(array $variables) {
        foreach ($variables as $key => $value) {
            $this->setVariable($key, $value);
        }
    }

    public function render(){
        ob_start();
        include($this->layoutPath);
        return ob_get_clean();
    }

    public function __toString(){
        return $this->render();
    }

    public function url($controller, $action, $query = null, $fullUri = false){

        if(preg_match('/^[a-zA-z]([a-zA-z]|\d)*$/m', $controller) !== 1)
            return null;

        if(preg_match('/^[a-zA-z]([a-zA-z]|\d)*$/m', $action) !== 1)
            return null;


        $url = Application::app()->request()->getScriptName();
        $url .= '/'.$controller.'/'.$action;


        if(is_array($query)){
            $queryParams = array();
            foreach ($query as $key => $value) {

                if(is_string($key))
                    $queryParams[] = $key.'='.$value;
                else
                    $queryParams[] = $value;
            }

            if(!empty($queryParams))
                $url .= '?'.implode('&', $queryParams);
        }

        return ($fullUri ? Application::app()->request()->getBaseUri() : '').$url;
    }
}
