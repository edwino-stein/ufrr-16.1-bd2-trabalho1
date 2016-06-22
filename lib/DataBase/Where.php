<?php
namespace DataBase;
use DataBase\Types;
use DataBase\TableSchema;

class Where{

    const BASE = 'WHERE ';

    protected $sqlBase;
    protected $params;
    public function __construct($whereSql, $params){

        if(!is_string($whereSql)) throw new \Exception('O parâmetro "$whereSql" deve ser uma string.', 1);
        if(!is_array($params)) throw new \Exception('O parâmetro "$params" deve ser um array.', 1);

        $this->sqlBase = $whereSql;
        $this->params = $params;
    }

    public function getSqlSnippet(TableSchema $tableSchema){

        if(empty($this->params) || $this->sqlBase === '') return '';

        $search = array();
        $replace = array();
        foreach ($this->params as $key => $value) {

            $columnSchema = $tableSchema->getColumn($key);

            $search[] = $key;
            $replace[] = $columnSchema['name'];

            $search[] = ':'.$columnSchema['name'];
            $replace[] = Types::prepareToQuery($value, $columnSchema['type']);

        }

        return self::BASE.str_replace($search, $replace, $this->sqlBase);
    }

    public static function parseArray($params){

        if(!is_array($params)) throw new \Exception('O parâmetro "$params" deve ser um array.', 1);
        $sql = array();
        foreach ($params as $key => $value)
            $sql[] = $key.' = :'.$key;

        return new self(implode(' and ', $sql), $params);
    }

}
