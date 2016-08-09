<?php
namespace Financas\Controller;
use Application\AbstractController;
use Financas\Util\ValidateException;
use Financas\Model\Financa;
use Financas\Model\DespesaReceitaMes;

class Mes extends AbstractController{

    public function indexAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');

        Financa::initMes($session->getData('id'));

        try {
            $meses = Financa::findBy(
                array('usuario' => $session->getData('id')),
                array('orderby' => 'mes', 'direction' => 'DESC')
            );
        } catch (\Exception $e) {
            $meses = array();
        }

        try {
            $data = DespesaReceitaMes::findAllByMes($session->getData('id'), $meses[0]->getMes());
        } catch (\Exception $e) {
            $data = array();
        }

        return self::getView(array(
            'data' => $data,
            'totalData' => count($data),
            'meses' => $meses,
            'mesesTotal' => count($meses)
        ));
    }
}
