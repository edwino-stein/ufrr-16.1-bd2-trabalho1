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

    public function createAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');

        return self::getView(array(
            'title' => 'Registrar despesa ou receita',
            'data' => null,
            'error' => false,
            'messages' => array()
        ), 'mes/form.phtml');
    }

    public function updateAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');


        $id = (int) $this->app()->request()->getQuery('financa_mes_id', 0);

        if($id <= 0){
            $model = null;
            $title = 'Registrar despesa ou receita';
        }
        else{
            $title = 'Editar despesa ou receita';

            try {
                $mes = Financa::getCurrentMes($session->getData('id'));
                $model = DespesaReceitaMes::findOneBy(array('id' => $id, 'financa' => $mes->getId()));
            } catch (\Exception $e) {
                return self::getView(array(
                    'title' => $title,
                    'data' => false,
                    'error' => true,
                    'messages' => array('generic' => "Um erro durante a operação com o banco de dados.")
                ), 'mes/form.phtml');
            }
        }

        return self::getView(array(
            'title' => $title,
            'data' => $model,
            'error' => false,
            'messages' => array()
        ), 'mes/form.phtml');
    }

    public function saveAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');

        $request = $this->app()->request();
        if(!$request->hasPost('mes-submit'))
            $this->app()->redirect($this->request()->getBaseUri().'index.php/mes/create');

        $id = (int) $request->getPost('mes-id', 0);
        $mes = Financa::getCurrentMes($session->getData('id'));

        if($id <= 0){
            $model = new DespesaReceitaMes();
            $model->setFinanca($mes);
        }
        else{
            $model = DespesaReceitaMes::findOneBy(array('id' => $id, 'financa' => $mes->getId()));
            if($model === null){
                $model = new DespesaReceitaMes();
                $model->setFinanca($mes);
                $id = 0;
            }
        }

        $title = $id <= 0 ? 'Registrar despesa ou receita' : 'Editar despesa ou receita';
        $erro = false;
        $messages = array();
        $type = $request->getPost('mes-tipo', 'receita');

        try {
            $model->setValor(((float) $request->getPost('mes-valor', 0))*($type != 'despesa' ? 1 : (-1)));
        } catch (ValidateException $e) {
            $erro = true;
            $messages['mes-valor'] = $e->getMessage();
        }

        try {
            $model->setDescricao($request->getPost('mes-descricao', null));
        } catch (ValidateException $e) {
            $erro = true;
            $messages['mes-descricao'] = $e->getMessage();
        }

        if($erro){
            return self::getView(array(
                'title' => $title,
                'data' => $model,
                'error' => $erro,
                'messages' => $messages
            ), 'mes/form.phtml');
        }

        try {
            $model->save();
        } catch (\Exception $e) {
            return self::getView(array(
                'title' => $title,
                'data' => $model,
                'error' => true,
                'messages' => array('generic' => "Um erro durante a operação com o banco de dados.")
            ), 'mes/form.phtml');
        }

        return self::getView(array(
            'title' => $title,
            'data' => false,
            'error' => false,
            'messages' => array('generic' => "A receita/despesa foi salva com sucesso.")
        ), 'mes/form.phtml');
    }

    public function deleteAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');

        $id = (int) $this->app()->request()->getQuery('financa_mes_id', 0);
        if($id <= 0)
            $this->app()->redirect($this->request()->getBaseUri().'index.php/mes/index');

        $error = false;
        $message = '';

        try {
            $mes = Financa::getCurrentMes($session->getData('id'));
            $model = DespesaReceitaMes::findOneBy(array('id' => $id, 'financa' => $mes->getId()));
        } catch (\Exception $e) {
            $error = true;
            $message = 'Um erro durante a operação com o banco de dados.';
        }

        if($model === null)
            $this->app()->redirect($this->request()->getBaseUri().'index.php/fixos/index');

        if($this->app()->request()->hasPost('mes-delete') && !$error){
            try {
                $model->delete();
            } catch (\Exception $e) {
                $error = true;
                $message = 'Não foi possivel remover a despesa/receita.';
            }

            $model = null;
        }

        return self::getView(array(
            'error' => false,
            'data' => $model,
            'messages' => $message
        ));
    }
}
