<?php
namespace Financas;
use Application\Application;
use Financas\Util\User;

class Financas extends Application{
    protected $user;
    protected $config = array(
        'db' => array(
            'driver' => 'pgsql',
            'dbname' => 'bd2_trabalho1',
            'user' => 'db2',
            'password' => '123456'
        ),
        'view' => array(
            'charset' => 'UTF-8',
            'contentName' => 'content',
            'noTemplate' => false,
            'defaultTemplate' => 'template.phtml'
        ),
        'defaultController' => 'main'
    );

    protected function init(){
        $this->user = User::init('user');
        parent::init();
    }

    public function user(){
        return $this->user;
    }
}
