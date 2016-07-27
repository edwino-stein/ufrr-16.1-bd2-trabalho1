<?php
namespace Financas\Model;
use DataBase\ModelBase;
use Financas\Util\ValidateException;

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
     * @notnull
     */
    protected $login;

    /**
     * @var string
     * @column senha
     * @length 32
     * @notnull
     */
    protected $senha;


    public function setNome($nome){

        if(empty($nome)) throw new ValidateException("nome", "O campo é obrigatório");
        if(strlen($nome) > 50) throw new ValidateException("nome", "Tamanho máximo é 50 catacteres.");

        $this->nome = $nome;
        return $this;
    }

    public function setSobrenome($sobrenome){
        $this->sobrenome = $sobrenome;
        return $this;
    }

    public function setLogin($login){

        if(empty($login)) throw new ValidateException("login", "O campo é obrigatório");
        if(strlen($login) > 20) throw new ValidateException("login", "Tamanho máximo é 20 catacteres.");
        if(strlen($login) < 5) throw new ValidateException("login", "Tamanho minimo é 5 catacteres.");

        $this->login = $login;
        return $this;
    }

    public function setSenha($senha){

        if(!preg_match('/^[a-f0-9]{32}$/i', $senha)){
            if(empty($senha)) throw new ValidateException("senha", "O campo é obrigatório");
            if(strlen($senha) < 6) throw new ValidateException("senha", "Tamanho minimo é 6 catacteres.");
            if(strlen($senha) > 20) throw new ValidateException("senha", "Tamanho máximo é 20 catacteres.");
        }

        $this->senha = $senha;
        return $this;
    }

    public function matchSenha($senha){
        return $this->senha === md5($senha);
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
