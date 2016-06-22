<?php
namespace Application;
use Application\Application;
use Application\TemplateConfig;
use Application\View;

class AbstractController {

    protected $defaultAction = 'index';

    public $controllerName;
    public $actionName;
    protected $template;

    protected static function app(){
        return Application::app();
    }

    protected static function getView(array $variables, $layoutPath = null){
        return new View($variables, $layoutPath);
    }

    public static function request(){
        return Application::app()->request();
    }

    public function db(){
        return Application::app()->db();
    }

    public function callAction($action){

        if(empty($action)){
            $action = $this->defaultAction;
        }

        $methods = get_class_methods($this);
        $upperName = strtoupper($action.'action');
        $method = null;

        foreach ($methods as $m) {
            if(strtoupper($m) ===  $upperName){
                $method = $m;
                break;
            }
        }

        if($method === null)
            throw new \Exception("Nenhuma action foi encontrado.", 1);

        $this->actionName = $action;
        $this->template = new TemplateConfig(self::app()->config()['view']);
        return $this->render($this->$method());
    }

    protected function render($view){

        if($view instanceof View){

            if($view->getLayout() === null)
                $view->setLayout(strtolower($this->controllerName).'/'.strtolower($this->actionName).'.phtml');

            if(!file_exists($view->getLayout())){

                if(file_exists(Application::getDir('moduleLoaded').'/View/'.$view->getLayout()))
                    $view->setLayout(Application::getDir('moduleLoaded').'/View/'.$view->getLayout());
                else
                    throw new \Exception("O aquivo View \"".Application::getDir('moduleLoaded').'/View/'.$view->getLayout()."\" é inválido.", 1);
            }
        }

        if($this->template === null || $this->template->getNoTemplate()) return $view;

        $content = is_string($this->template->getContentName()) ? $this->template->getContentName(): 'content';
        return new View(array(
            $content => $view,
            'title' => $this->template->getTitle(),
            'charset' => $this->template->getCharset()
        ), Application::getDir('moduleLoaded').'/View/'.$this->template->getLayoutPath());
    }
}
