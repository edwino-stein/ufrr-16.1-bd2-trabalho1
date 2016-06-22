<?php
namespace Financas\Controller;
use Application\AbstractController;

class Main extends AbstractController{

    public function indexAction(){
        return self::getView(array());
    }
}
