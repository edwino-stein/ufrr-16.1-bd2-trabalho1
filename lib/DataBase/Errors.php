<?php

namespace DataBase;

use DataBase\DataBaseException;

abstract class Errors{

    const UNKNOW_ERROR = 0;

    //Erros de conexão
    const CONNECTION_ERROR = 100;
    const DRIVER_INVALID = 101;
    const DATA_BASE_INVALID = 102;
    const USER_OR_PASSWORD_INVALID = 103;
    const CONNECTION_NOT_SETTED = 104;

    //Erros de SQLs
    const CANT_CREATE_QUERY = 200;
    const SYNTAX_ERROR  = 201;
    const TABLE_NOT_FOUND = 203;
    const COLUMN_NOT_FOUND = 204;

    //Erros da abstração
    const ID_NOT_DEFINED = 300;
    const PROPERTY_NOT_NULL = 301;
    const LENGTH_OVERFLOW = 302;
    const WHERE_PARAM_INVALID = 303;

    //Menssagens
    protected static $messages = array(
        self::UNKNOW_ERROR => 'Um erro desconhecido ocorreu.',

        self::CONNECTION_ERROR => 'Um erro ocorreu durante a tentativa de conexão com o banco de dados.',
        self::DRIVER_INVALID => 'O driver do bando de dados é inválido ou incompativel.',
        self::DATA_BASE_INVALID => 'O bando de dados não existe ou é inválido.',
        self::USER_OR_PASSWORD_INVALID => 'O usuário ou senha são inválidos.',
        self::CONNECTION_NOT_SETTED => 'A conexão como bando de dados não foi inicializada ou configurada.',

        self::CANT_CREATE_QUERY => 'Não foi possivel preparar a query.',
        self::TABLE_NOT_FOUND => 'A tabela não foi definida.',
        self::COLUMN_NOT_FOUND => 'A coluna da entidade é inválida ou foi definida.',
        self::SYNTAX_ERROR => 'Sintax inválida na query.',

        self::ID_NOT_DEFINED => 'Nenhum identificador foi definido para a entidade "{entity}".',
        self::PROPERTY_NOT_NULL => 'A properiedade "{property}" não pode ser NULL.',
        self::LENGTH_OVERFLOW => 'A propriedade "{property}" excedeu o comprimento máximo {length}.',
        self::WHERE_PARAM_INVALID => 'O parâmetro "$where" deve ser um array ou uma instância de DataBase\Where.',
    );


    public static function getException($code, $prev = null){
        if(!isset(self::$messages[$code])) $code = self::UNKNOW_ERROR;
        return new DataBaseException($code, self::$messages[$code], $prev);
    }

    public static function getExceptionWithDetails($code, $params, $prev = null){
        if(!isset(self::$messages[$code])) $code = self::UNKNOW_ERROR;

        $marckup = array();
        $values = array();

        foreach ($params as $key => $value) {
            $marckup[] = $key;
            $values[] = $value;
        }

        $message = str_replace($marckup, $values, self::$messages[$code]);
        return new DataBaseException($code, $message, $prev);
    }
}
