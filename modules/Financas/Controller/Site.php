<?php
namespace Financas\Controller;
use Application\AbstractController;
use Financas\Model\Usuario;
use Financas\Util\ValidateException;

class Site extends AbstractController{

    public function indexAction(){
        $session = $this->app()->user();
        $this->template->setLayoutPath('site-template.phtml');

        if(!$session->isGuest())
            $this->app()->redirect($this->request()->getBaseUri().'index.php/mes/index');

        return self::getView(array());
    }

    public function loginAction(){

        $this->template->setLayoutPath('site-template.phtml');

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
        $this->template->setLayoutPath('site-template.phtml');
        $this->app()->user()->clean();
        $this->app()->redirect($this->request()->getBaseUri().'index.php');
    }

    public function singupAction(){
        $this->template->setLayoutPath('site-template.phtml');

        $session = $this->app()->user();
        if(!$session->isGuest()){
            $this->app()->redirect($this->request()->getBaseUri().'index.php');
            return;
        }

        if($this->app()->request()->hasPost('singup-submit')){

            $newUser = new Usuario();
            $errors = array();
            $map = array(
                'singup-name' => 'nome',
                'singup-lastname' => 'sobrenome',
                'singup-login' => 'login',
                'singup-passwd' => 'senha'
            );

            foreach ($map as $input => $property){
                $method = 'set'.ucfirst($property);
                try {
                    $newUser->$method($this->app()->request()->getPost($input, null));
                } catch (ValidateException $e) {
                    $errors[$input] = $e->getMessage();
                }
            }

            if(!empty($errors))return self::getView(array(
                'error' => true,
                'data' => $newUser,
                'messages' => $errors
            ));

            try {
                $newUser->save();
            } catch (\Exception $e) {

                $messages = array();
                $exMessage = $e->getPrevException()->getMessage();
                $exCode = $e->getPrevException()->getCode();

                if($exCode == 23505){
                    $messages['singup-login'] = explode(': ', $exMessage)[3];
                }

                return self::getView(array(
                    'error' => true,
                    'data' => $newUser,
                    'messages' => $messages
                ));
            }

            $session->setData(array(
                'id' => $newUser->getId(),
                'nome' => $newUser->getNome(),
                'sobrenome' => $newUser->getSobrenome(),
                'login' => $newUser->getLogin()
            ));

            return self::getView(array(
                'error' => false,
                'data' => $newUser,
                'messages' => array('success' => 'Usuário cadastrado com sucesso!')
            ));
        }

        return self::getView(array(
            'error' => false,
            'data' => null,
            'messages' => array()
        ));
    }
}
