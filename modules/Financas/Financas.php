<?php
namespace Financas;
use Application\Application;

class Financas extends Application{
    protected $config = array(
        'db' => array(),
        'view' => array(
            'charset' => 'UTF-8',
            'contentName' => 'content',
            'noTemplate' => false,
            'defaultTemplate' => 'template.phtml'
        ),
        'defaultController' => 'main'
    );
}
