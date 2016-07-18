<?php
namespace Financas\Model;
use DataBase\ModelBase;
use DataBase\Types;

/**
 * @table dispesas_receitas
 */
class DispesaReceita extends ModelBase{

    /**
     * @var int
     * @id
     * @column id
     */
    protected $id;

    /**
     * @var string
     * @column descricao
     * @length 20
     * @notnull
     */
    protected $descricao;

    /**
     * @var float
     * @column valor
     * @notnull
     */
    protected $valor;

    public function getId(){
        return $this->id;
    }

    public function getDescricao(){
        return $this->descricao;
    }

    public function getValor(){
        return $this->valor;
    }

    public function setDescricao($descricao){
        $this->descricao = $descricao;
        return $this;
    }

    public function setValor($valor){
        $this->valor = $valor;
        return $this;
    }
}
