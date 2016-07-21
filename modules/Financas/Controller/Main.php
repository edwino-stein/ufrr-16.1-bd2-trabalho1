<?php
namespace Financas\Controller;
use Application\AbstractController;
use Financas\Model\Usuario;

class Main extends AbstractController{

    public function indexAction(){
        return self::getView(array());
    }

    public function loginAction(){

        $session = $this->app()->user();
        if(!$session->isGuest()) return self::getView(array(
            'error' => false,
            'message' => "Você já está logado como  <b>".$session->getData('nome')."</b>."
        ));

        $login = $this->app()->request()->getPost('user-login', null);
        if(empty($login)) return self::getView(array(
            'error' => true,
            'message' => "O campo <b>Login</b> deve ser preenchido."
        ));

        $password = $this->app()->request()->getPost('user-passwd', null);
        if(empty($password)) return self::getView(array(
            'error' => true,
            'message' => "O campo <b>Senha</b> deve ser preenchido."
        ));

        $user = Usuario::findOneBy(array('login' => $login));
        if($user === null) return self::getView(array(
            'error' => true,
            'message' => "Usuário informado é inválido."
        ));

        if(!$user->matchSenha($password)) return self::getView(array(
            'error' => true,
            'message' => "A senha informada é inválida."
        ));

        $session->setData(array(
            'id' => $user->getId(),
            'nome' => $user->getNome(),
            'sobrenome' => $user->getSobrenome(),
            'login' => $user->getLogin()
        ));

        return self::getView(array(
            'error' => false,
            'message' => "Bem vindo <b>".$user->getNome()."</b>."
        ));
    }

    public function logoutAction(){
        $this->app()->user()->clean();
        $this->app()->redirect($this->request()->getBaseUri().'index.php');
    }
}
