<?php
namespace Financas\Model;
use DataBase\Connection;
use DataBase\Errors;
use DataBase\ModelBase;
use DataBase\Types;
use Financas\Model\Usuario;

/**
 * @table financas
 */
class Financa extends ModelBase{

    /**
     * @var int
     * @id
     * @column id
     */
    protected $id;

    /**
     * @var int
     * @column usuario_id
     * @notnull
     */
    protected $usuario;

    /**
     * @var datetime
     * @column mes
     * @notnull
     */
    protected $mes;

    public function setUsuario($usuario){
        if($usuario instanceof Usuario)
            $this->usuario = $usuario->getId();
        else
            $this->usuario = (int) $usuario;
        return $this;
    }

    public function setMes($mes){
        if($mes instanceof \DateTime)
            $this->mes = $mes;
        $this->mes = Types::toDate($mes);
        return $this;
    }

    public function getId(){
        return $this->id;
    }

    public function getUsuario($instance = false){
        return $instance ? Usuario::findOneBy(array('id' => $this->usuario)) : $this->usuario;
    }

    public function getMes(){
        return $this->mes;
    }

    public static function initMes($usuario){

        if(!Connection::isInited()) throw Errors::getException(Errors::CONNECTION_NOT_SETTED);

        $usuario = $usuario instanceof Usuario ? $usuario->getId() : (int) $usuario;
        $query = Connection::getConnection()->createQuery('select init_mes('.$usuario.')');

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

        return $query->fetch(\PDO::FETCH_OBJ)->init_mes == 1;
    }

    public static function getCurrentMes($usuario){

        if(!Connection::isInited()) throw Errors::getException(Errors::CONNECTION_NOT_SETTED);

        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);
        $usuario = $usuario instanceof Usuario ? $usuario->getId() : (int) $usuario;
        $query = Connection::getConnection()->createQuery('SELECT * FROM financas WHERE financas.usuario_id = '.$usuario.' and EXTRACT(MONTH FROM financas.mes) = EXTRACT(MONTH FROM now()) and EXTRACT(YEAR FROM financas.mes) = EXTRACT(YEAR FROM now())');

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

        $row = $query->fetch(\PDO::FETCH_OBJ);
        return $row ? self::getModelInstance($modelName, $tableSchema, $row) : null;
    }

}
