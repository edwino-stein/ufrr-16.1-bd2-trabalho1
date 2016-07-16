<?php
namespace Financas\Model;
use DataBase\ModelBase;
use DataBase\Types;
use Financas\Model\Usuario;

/**
 * @table financas
 */
class Financas extends ModelBase{

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
}
