<?php
namespace Financas\Model;

use DataBase\ModelBase;
use DataBase\Types;
use Financas\Model\Financa;

/**
 * @table despesas_receitas_mes
 * @seq dispesas_receitas_id_seq
 */
class DispesaReceitaMes extends ModelBase{

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
     * @column financas_id
     * @notnull
     */
    protected $financa;

    public function getId(){
        return $this->id;
    }

    public function getDescricao(){
        return $this->descricao;
    }

    public function getValor(){
        return $this->valor;
    }

    public function getFinanca($instance = false){
        return $instance ? Financa::findOneBy(array('id' => $this->financa)) : $this->financa;
    }

    public function setDescricao($descricao){
        $this->descricao = $descricao;
        return $this;
    }

    public function setValor($valor){
        $this->valor = Types::casting($valor, 'float');
        return $this;
    }

    public function setFinanca($financa){

        if($financa instanceof Financa)
            $this->financa = $financa->getId();
        else
            $this->financa = Types::casting($financa, 'int');

        return $this;
    }
}
