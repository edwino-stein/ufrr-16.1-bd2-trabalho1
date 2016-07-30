<?php
namespace Financas\Controller;
use Application\AbstractController;
use Financas\Model\DispesaReceitaFixa;
use Financas\Util\ValidateException;

class Fixos extends AbstractController{

    public function indexAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');

        $fixos = DispesaReceitaFixa::findBy(
            array('usuario' => $session->getData('id')),
            array('orderby' => 'valor', 'direction' => 'DESC')
        );

        return self::getView(array(
            'data' => $fixos,
            'total' => count($fixos)
        ));
    }
}
