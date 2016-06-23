<?php
namespace Financas;
use Application\Application;

class Financas extends Application{
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
}
