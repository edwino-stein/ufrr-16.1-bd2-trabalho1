<?php
namespace Financas\Model;

use DataBase\Connection;
use DataBase\Errors;
use DataBase\ModelBase;
use DataBase\Types;
use Financas\Model\Financa;
use Financas\Model\Usuario;
use Financas\Util\ValidateException;

/**
 * @table despesas_receitas_mes
 * @seq despesas_receitas_id_seq
 */
class DespesaReceitaMes extends ModelBase{

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
        if(empty($descricao)) throw new ValidateException("descricao", "O campo é obrigatório.");
        if(strlen($descricao) > 100) throw new ValidateException("descricao", "Tamanho máximo é 100 caractere.");
        return $this;
    }

    public function setValor($valor){
        $this->valor = Types::casting($valor, 'float');
        if((float) $valor == 0) throw new ValidateException("valor", "O valor deve ser diferente de zero.");
        return $this;
    }

    public function setFinanca($financa){
        $this->financa = $financa instanceof Financa ? $financa->getId() : Types::casting($financa, 'int');
        return $this;
    }

    public static function findAllByMes($usuario, \DateTime $mes){

        if(!Connection::isInited()) throw Errors::getException(Errors::CONNECTION_NOT_SETTED);

        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);

        $month = (int) $mes->format('m');
        $year = (int) $mes->format('Y');
        $usuario = $usuario instanceof Usuario ? $usuario->getId() : (int) $usuario;

        $query = Connection::getConnection()->createQuery('SELECT * FROM despesas_receitas_mes_view WHERE EXTRACT(MONTH FROM despesas_receitas_mes_view.mes) = '.$month.' and EXTRACT(YEAR FROM despesas_receitas_mes_view.mes) = '.$year.' and usuario_id = '.$usuario);

        //Executa a query ou captura o erro causado
        try{
            $query->execute();
        }
        catch(\Exception $e){
            $code = 0;
            switch ($e->getCode()) {
                case '42S02':
                    $code = Errors::TABLE_NOT_FOUND;
                break;

                case '42S22':
                    $code = Errors::COLUMN_NOT_FOUND;
                break;

                case '42000':
                    $code = Errors::SYNTAX_ERROR;
                break;

                default:
                    $code = Errors::UNKNOW_ERROR;
                break;
            }

            throw Errors::getException($code, $e);
        }

        //Parseia as tuplas recuperadas e instancia as models
        $data = array();
        while($row = $query->fetch(\PDO::FETCH_OBJ))
            $data[] = self::getModelInstance($modelName, $tableSchema, $row);

        return $data;
    }
}
