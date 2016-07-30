<?php
namespace Financas\Controller;
use Application\AbstractController;
use Financas\Util\ValidateException;

class Mes extends AbstractController{

    public function indexAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');

        return self::getView(array());
    }
}
