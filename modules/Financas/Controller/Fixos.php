<?php
namespace Financas\Controller;
use Application\AbstractController;
use Financas\Model\DespesaReceitaFixa;
use Financas\Util\ValidateException;

class Fixos extends AbstractController{

    public function indexAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');

        try {
            $fixos = DespesaReceitaFixa::findBy(
                array('usuario' => $session->getData('id')),
                array('orderby' => 'valor', 'direction' => 'DESC')
            );
        } catch (\Exception $e) {
            $fixos = array();
        }



        return self::getView(array(
            'data' => $fixos,
            'total' => count($fixos)
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
        ), 'fixos/form.phtml');
    }

    public function updateAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');


        $id = (int) $this->app()->request()->getQuery('fixo_id', 0);

        if($id <= 0){
            $model = null;
            $title = 'Registrar despesa ou receita';
        }
        else{
            $model = DespesaReceitaFixa::findOneBy(array('id' => $id, 'usuario' => $session->getData('id')));
            $title = 'Editar despesa ou receita';
        }

        return self::getView(array(
            'title' => $title,
            'data' => $model,
            'error' => false,
            'messages' => array()
        ), 'fixos/form.phtml');
    }

    public function saveAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');

        $request = $this->app()->request();
        if(!$request->hasPost('fixo-submit'))
            $this->app()->redirect($this->request()->getBaseUri().'index.php/fixos/create');

        $id = (int) $request->getPost('fixo-id', 0);

        if($id <= 0){
            $model = new DespesaReceitaFixa();
            $model->setUsuario($session->getData('id'));
        }
        else{
            $model = DespesaReceitaFixa::findOneBy(array('id' => $id, 'usuario' => $session->getData('id')));
            if($model === null){
                $model = new DespesaReceitaFixa();
                $model->setUsuario($session->getData('id'));
                $id = 0;
            }
        }

        $title = $id <= 0 ? 'Registrar despesa ou receita' : 'Editar despesa ou receita';
        $erro = false;
        $messages = array();
        $type = $request->getPost('fixo-tipo', 'receita');

        try {
            $model->setValor(((float) $request->getPost('fixo-valor', 0))*($type != 'despesa' ? 1 : (-1)));
        } catch (ValidateException $e) {
            $erro = true;
            $messages['fixo-valor'] = $e->getMessage();
        }

        try {
            $model->setDescricao($request->getPost('fixo-descricao', null));
        } catch (ValidateException $e) {
            $erro = true;
            $messages['fixo-descricao'] = $e->getMessage();
        }

        if($erro){
            return self::getView(array(
                'title' => $title,
                'data' => $model,
                'error' => $erro,
                'messages' => $messages
            ), 'fixos/form.phtml');
        }

        try {
            $model->save();
        } catch (\Exception $e) {
            var_dump($e);
            return self::getView(array(
                'title' => $title,
                'data' => $model,
                'error' => true,
                'messages' => array('generic' => "Um erro durante a operação com o banco de dados.")
            ), 'fixos/form.phtml');
        }

        return self::getView(array(
            'title' => $title,
            'data' => false,
            'error' => false,
            'messages' => array('generic' => "A receita/despesa foi salva com sucesso.")
        ), 'fixos/form.phtml');
    }

    public function deleteAction(){

        $session = $this->app()->user();
        if($session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php');

        $id = (int) $this->app()->request()->getQuery('fixo_id', 0);
        if($id <= 0)
            $this->app()->redirect($this->request()->getBaseUri().'index.php/fixos/index');

        $model = DespesaReceitaFixa::findOneBy(array('id' => $id, 'usuario' => $session->getData('id')));
        if($model === null)
            $this->app()->redirect($this->request()->getBaseUri().'index.php/fixos/index');

        $error = false;
        $message = '';

        if($this->app()->request()->hasPost('fixo-delete')){
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
