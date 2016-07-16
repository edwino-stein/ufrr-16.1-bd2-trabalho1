<?php
namespace Financas\Model;
use DataBase\ModelBase;

/**
 * @table usuarios
 */
class Usuario extends ModelBase{

    /**
     * @var int
     * @id
     * @column id
     */
    protected $id;

    /**
     * @var string
     * @column nome
     * @length 50
     * @notnull
     */
    protected $nome;

    /**
     * @var string
     * @column sobrenome
     * @length 50
     */
    protected $sobrenome;

    /**
     * @var string
     * @column login
     * @length 20
     */
    protected $login;

    /**
     * @var string
     * @column senha
     * @length 32
     */
    protected $senha;


    public function setNome($nome){
        $this->nome = $nome;
        return $this;
    }

    public function setSobrenome($sobrenome){
        $this->sobrenome = $sobrenome;
        return $this;
    }

    public function setLogin($login){
        $this->login = $login;
        return $this;
    }

    public function setSenha($senha){
        $this->senha = md5($senha);
        return $this;
    }

    public function getId(){
        return $this->id;
    }

    public function getNome(){
        return $this->nome;
    }

    public function getSobrenome(){
        return $this->sobrenome;
    }

    public function getLogin(){
        return $this->login;
    }

    public function getSenha(){
        return $this->senha;
    }
}
