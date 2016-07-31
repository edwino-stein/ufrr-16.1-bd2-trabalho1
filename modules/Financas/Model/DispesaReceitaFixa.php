<?php
namespace Financas\Model;

use DataBase\ModelBase;
use DataBase\Types;
use Financas\Model\Usuario;
use Financas\Util\ValidateException;

/**
 * @table dispesas_receitas_fixas
 * @seq dispesas_receitas_id_seq
 */
class DispesaReceitaFixa extends ModelBase{

    /**
     * @var int
     * @id
     * @column id
     */
    protected $id;

    /**
     * @var string
     * @column descricao
     * @length 100
     * @notnull
     */
    protected $descricao;

    /**
     * @var float
     * @column valor
     * @notnull
     */
    protected $valor;

    /**
     * @var int
     * @column usuario_id
     * @notnull
     */
    protected $usuario;

    public function getId(){
        return $this->id !== null ? $this->id : 0;
    }

    public function getDescricao(){
        return $this->descricao;
    }

    public function getValor(){
        return $this->valor;
    }

    public function getUsuario($instance = false){
        return $instance ? Usuario::findOneBy(array('id' => $this->usuario)) : $this->usuario;
    }

    public function setDescricao($descricao){

        $this->descricao = $descricao;
        if(empty($descricao)) throw new ValidateException("descricao", "O campo é obrigatório.");
        if(strlen($descricao) > 100) throw new ValidateException("descricao", "Tamanho máximo é 100 caractere.");

        return $this;
    }

    public function setValor($valor){

        $this->valor = Types::casting($valor, 'float');
        if((float) $valor == 0) throw new ValidateException("valor", "O valor deve ser diferente de zero.");

        return $this;
    }

    public function setUsuario($usuario){
        if($usuario instanceof Usuario)
            $this->usuario = $usuario->getId();
        else
            $this->usuario = (int) $usuario;
        return $this;
    }
}
