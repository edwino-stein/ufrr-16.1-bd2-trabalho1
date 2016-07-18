<?php
namespace DataBase;

use DataBase\Connection;
use DataBase\Errors;
use DataBase\TableSchema;
use DataBase\Types;
use DataBase\Where;

/**
 * Classe que serve como base para uma model
 */
abstract class ModelBase{

    /* ********************** Trechos de SQL ********************** */
    const ORDER_DIRECTION_ASC = 'ASC';
    const ORDER_DIRECTION_DESC = 'DESC';
    const GENERIC_SELECT = 'SELECT * FROM {table} {order} {direction} {limit}';
    const FIND_SELECT = 'SELECT * FROM {table} {where} {order} {direction} {limit}';
    const INSERT = 'INSERT INTO {table} ({columns}) VALUES ({data})';
    const UPDATE = 'UPDATE {table} SET {setters} {where}';
    const DELETE = 'DELETE FROM {table} {where}';
    /* ************************************************************ */

    /**
     * Esquemas armazenados das models utilizadas.
     * @var array
     */
    private static $schemas = array();

    /**
     * Flag para indicar se a model foi carregado a partir do bando de dados.
     * @var bool
     */
    private $__loaded__ = false;

    /**
     * Método para salvar os dados da model no banco.
     * Dependendo da flag $__loaded__, irá realizar um CREATE ou um UPDATE.
     * @throws DataBase\DataBaseException Em caso de falha é lançada instâncias de DataBase\DataBaseException contendo as informações do erro.
     * @return bool true para sucesso.
     */
    public function save(){
        return $this->__loaded__ ? $this->update() : $this->insert();
    }

    /**
     * Remove um registro do banco de dados.
     * @throws DataBase\DataBaseException Em caso de falha é lançada instâncias de DataBase\DataBaseException contendo as informações do erro.
     * @return bool true para sucesso.
     */
    public function delete(){

        //Verifica se a conexão foi configurada
        if(!Connection::isInited()) throw Errors::getException(Errors::CONNECTION_NOT_SETTED);

        //Verifica se a model foi carregada do bando de dados
        if(!$this->__loaded__) return true;

        //Pega o esquema da model
        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);
        $idColumn = null;
        $tableSchema->getIdColumn($idColumn);

        //Verifica se existe alguma propriedade definida como ID
        if($idColumn === null)
            throw Errors::getExceptionWithDetails(Errors::ID_NOT_DEFINED, array('{entity}' => $modelName));

        //Cria um Where a partir do ID da model
        $where = Where::parseArray(array($idColumn => $this->_get($idColumn)));

        //Executa a query de remoção
        $result = self::execQuery(
            array('{table}', '{where}'),
            array(
                $tableSchema->getTableName(),
                $where->getSqlSnippet($tableSchema)
            ),
            self::DELETE
        );

        // Desativa a flag $__loaded__
        $this->_setLoaded(false);
        return true;
    }

    /**
     * Atualiza os dados de um registro no banco de dados.
     * @throws DataBase\DataBaseException Em caso de falha é lançada instâncias de DataBase\DataBaseException contendo as informações do erro.
     * @return bool true para sucesso.
     */
    public function update(){

        //Verifica se a conexão foi configurada
        if(!Connection::isInited()) throw Errors::getException(Errors::CONNECTION_NOT_SETTED);

        //Se a flag $__loaded__ estiver desativada, realiza um CREATE
        if(!$this->__loaded__) return $this->insert();

        //Pega o esquema da model
        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);

        //Pega as propriedades das colunas
        $schemaCols = $tableSchema->getColumns();
        $setters = array();
        $idColumn = null;
        $value = null;

        //Serializa as colunas
        foreach ($schemaCols as $property => $schema){

            //Guarda o id
            if($schema['id']){
                $idColumn = $property;
                continue;
            }

            //Converte o valor da coluna para o formato utilizado na SQL
            $value = Types::prepareToQuery($this->_get($property), $schema['type']);

            //Verifica se a coluna está definida como notnull
            if($schema['notnull'] && $value === Types::NULL_TYPE){
                throw Errors::getExceptionWithDetails(
                    Errors::PROPERTY_NOT_NULL,
                    array('{property}' => $property)
                );
            }

            //Verifica o comprimento do valor da coluna
            if($schema['length'] !== null && $schema['type'] === 'string' && (strlen($value) - 2) >  $schema['length']){
                throw Errors::getExceptionWithDetails(
                    Errors::LENGTH_OVERFLOW,
                    array('{property}' => $property, '{length}' => $schema['length'])
                );
            }

            //Garda a coluna e o valor
            $setters[] = $schema['name'].' = '.$value;
        }

        //Verifica se existe alguma propriedade definida como ID
        if($idColumn === null)
            throw Errors::getExceptionWithDetails(Errors::ID_NOT_DEFINED, array('{entity}' => $modelName));

        //Cria um Where a partir do ID da model
        $where = Where::parseArray(array($idColumn => $this->_get($idColumn)));

        //Executa a query de atualização
        $result = self::execQuery(
            array('{table}', '{setters}', '{where}'),
            array(
                $tableSchema->getTableName(),
                implode(', ', $setters),
                $where->getSqlSnippet($tableSchema)
            ),
            self::UPDATE
        );

        return true;
    }

    /**
     * Insere dados de um registro no bando de dados.
     * @throws DataBase\DataBaseException Em caso de falha é lançada instâncias de DataBase\DataBaseException contendo as informações do erro.
     * @return bool true para sucesso.
     */
    public function insert(){

        //Verifica se a conexão foi configurada
        if(!Connection::isInited()) throw Errors::getException(Errors::CONNECTION_NOT_SETTED);

        //Se a flag $__loaded__ estiver ativada, realiza um UPDATE
        if($this->__loaded__) return $this->update();

        //Pega o esquema da model
        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);

        //Pega as propriedades das colunas
        $schemaCols = $tableSchema->getColumns();
        $columns = array();
        $data = array();
        $value = null;
        $idColumn = null;

        //Serializa as colunas
        foreach ($schemaCols as $property => $schema){

            //Guarda o id e define o valor como NULL
            if($schema['id']){
                $idColumn = $property;
                continue;
            }

            $columns[] = $schema['name'];

            //Converte o valor da coluna para o formato utilizado na SQL
            $value = Types::prepareToQuery($this->_get($property), $schema['type']);

            //Verifica se a coluna está definida como notnull
            if($schema['notnull'] && $value === Types::NULL_TYPE){
                throw Errors::getExceptionWithDetails(
                    Errors::PROPERTY_NOT_NULL,
                    array('{property}' => $property)
                );
            }

            //Verifica o comprimento do valor da coluna
            if($schema['length'] !== null && $schema['type'] === 'string' && (strlen($value) - 2) >  $schema['length']){
                throw Errors::getExceptionWithDetails(
                    Errors::LENGTH_OVERFLOW,
                    array('{property}' => $property, '{length}' => $schema['length'])
                );
            }

            $data[] = $value;
        }

        //Executa a query de incerção
        $result = self::execQuery(
            array('{table}', '{columns}', '{data}'),
            array(
                $tableSchema->getTableName(),
                implode(', ', $columns),
                implode(', ', $data)
            ),
            self::INSERT
        );

        //Se existir uma coluna id, define o valor na model
        if($idColumn !== null){
            $this->_set(
                $idColumn,
                Connection::getLastInsertId($tableSchema),
                $schemaCols[$idColumn]
            );
        }

        //Ativa a flag $__loaded__
        $this->_setLoaded(true);
        return true;
    }

    /**
     * Busta todos os registros da tabela.
     * @param  array  $options Define as instruções ORDER BY e LIMMIT
     * @return array  Lista de models recuperadas do banco de dados
     */
    public static function fetchAll($options = array()){

        //Verifica se a conexão foi configurada
        if(!Connection::isInited()) throw Errors::getException(Errors::CONNECTION_NOT_SETTED);

        //Pega o esquema da model
        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);

        //Chega as opções
        $options = self::parseOptions($options, $tableSchema);

        //Realia a operação SELECT
        $result = self::execQuery(
            array('{table}', '{order}', '{direction}', '{limit}'),
            array(
                $tableSchema->getTableName(),
                $options['orderby'],
                $options['direction'],
                $options['limit'],
            ),
            self::GENERIC_SELECT
        );

        //Parseia as tuplas recuperadas e instancia as models
        $data = array();
        while($row = $result->fetch(\PDO::FETCH_OBJ))
            $data[] = self::getModelInstance($modelName, $tableSchema, $row);

        return $data;
    }

    /**
     * Busca todos os registros com um criterio.
     * @param  array|DataBase\Where $where   Criterio de consulta
     * @param  array  $options Define as instruções ORDER BY e LIMIT
     * @return array  Lista de models recuperadas do banco de dados
     */
    public static function findBy($where, $options = array()){

        //Verifica se a conexão foi configurada
        if(!Connection::isInited()) throw Errors::getException(Errors::CONNECTION_NOT_SETTED);

        //Pega o esquema da model
        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);

        //Chega as opções
        $options = self::parseOptions($options, $tableSchema);

        //Se o parametro $where for um array, instancia um objeto de DataBase\Where
        if(is_array($where)) $where = Where::parseArray($where);
        if(!($where instanceof Where)) throw Errors::getException(Errors::WHERE_PARAM_INVALID);

        //Realia a operação SELECT
        $result = self::execQuery(
            array('{table}', '{where}', '{order}', '{direction}', '{limit}'),
            array(
                $tableSchema->getTableName(),
                $where->getSqlSnippet($tableSchema),
                $options['orderby'],
                $options['direction'],
                $options['limit'],
            ),
            self::FIND_SELECT
        );

        //Parseia as tuplas recuperadas e instancia as models
        $data = array();
        while($row = $result->fetch(\PDO::FETCH_OBJ))
            $data[] = self::getModelInstance($modelName, $tableSchema, $row);

        return $data;
    }

    /**
     * Busca apenas um registro com um criterio.
     * @param  array|DataBase\Where $where   Criterio de consulta
     * @return array|null  Lista de models recuperadas do banco de dados
     */
    public static function findOneBy($where){

        //Verifica se a conexão foi configurada
        if(!Connection::isInited()) throw Errors::getException(Errors::CONNECTION_NOT_SETTED);

        //Pega o esquema da model
        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);

        $options = self::parseOptions(array('limit' => 1, 'offset' => 0), $tableSchema);

        //Se o parametro $where for um array, instancia um objeto de DataBase\Where
        if(is_array($where)) $where = Where::parseArray($where);
        if(!($where instanceof Where)) throw Errors::getException(Errors::WHERE_PARAM_INVALID);

        //Realia a operação SELECT
        $result = self::execQuery(
            array('{table}', '{where}', '{order}', '{direction}', '{limit}'),
            array(
                $tableSchema->getTableName(),
                $where->getSqlSnippet($tableSchema),
                $options['orderby'],
                $options['direction'],
                $options['limit'],
            ),
            self::FIND_SELECT
        );

        //Instancia a model retornada
        $row = $result->fetch(\PDO::FETCH_OBJ);
        return $row ? self::getModelInstance($modelName, $tableSchema, $row) : null;
    }

    /**
     * Prepara e executa uma query
     * @param  array $params  Lista de paramtros que serão subistituidos por valores na query
     * @param  array $values  Valores que serão inseridos na query
     * @param  string $sqlBase Template da SQL
     * @return \PDOStatement          Resultado da query
     */
    protected static function execQuery($params, $values, $sqlBase){

        //Prepara a query
        $sql = str_replace($params, $values, $sqlBase);
        $query = Connection::getConnection()->createQuery($sql);

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

        return $query;
    }

    /**
     * Constroi ou pega o esquema da tabela
     * @param  string $modelName Nome da class models
     * @return DataBase\TableSchema            Esquema da tabela
     */
    protected static function getTableSchema($modelName = null){
        if($modelName === null) $modelName = get_called_class();
        if(isset(self::$schemas[$modelName])) return self::$schemas[$modelName];
        self::$schemas[$modelName] = new TableSchema($modelName);
        return self::$schemas[$modelName];
    }

    /**
     * Parseia e verifica as opções ORDER BY e LIMIT
     * @param  array $options     opções
     * @param  DataBase\TableSchema $tableSchema
     * @return array
     */
    protected static function parseOptions($options, $tableSchema){

        // Se não tiver opçoes, pega a padão
        if(!is_array($options)){
            $options = array();
            $idColumn = $tableSchema->getIdColumn();

            $options['orderby'] = $idColumn === null ? '' : 'ORDER BY '.$idColumn['name'];
            $options['direction'] = $idColumn === null ? '' : self::ORDER_DIRECTION_ASC;
            $options['limit'] = '';
            return $options;
        }

        //Descobre o order by
        if(!isset($options['orderby']) || !$tableSchema->hasColumn($options['orderby'])){
            $idColumn = $tableSchema->getIdColumn();
            $options['orderby'] = $idColumn === null ? '' : 'ORDER BY '.$idColumn['name'];
        }
        else{
            $options['orderby'] = 'ORDER BY '.$tableSchema->getColumn($options['orderby'])['name'];
        }

        //descobre o criterio de ordenação
        if($options['orderby'] === ''){
            $options['direction'] = '';
        }
        else if(!isset($options['direction'])){
            $options['direction'] = self::ORDER_DIRECTION_ASC;
        }

        else if(strtoupper($options['direction']) !== self::ORDER_DIRECTION_ASC && strtoupper($options['direction']) !== self::ORDER_DIRECTION_DESC){
            $options['direction'] = self::ORDER_DIRECTION_ASC;
        }
        else{
            $options['direction'] = strtoupper($options['direction']);
        }

        //Limite e offset
        $limit = isset($options['limit']) ? (int) $options['limit'] : 0;
        $offset = isset($options['offset']) ? (int) $options['offset'] : 0;
        unset($options['offset']);

        if(Connection::getConnection()->config('driver') === 'pgsql'){
            if($limit > 0){
                $options['limit'] = 'LIMIT '.$limit.($offset >= 0 ? ' OFFSET '.$offset : '');
            }
            else{
                $options['limit'] = '';
            }
        }
        else{
            if($limit > 0){
                $options['limit'] = 'LIMIT '.($offset >= 0 ? $offset.',' : '0,').$limit;
            }
            else{
                $options['limit'] = '';
            }
        }

        return $options;
    }

    /**
     * Instancia models e as inicializa
     * @param  string $modelName   Nome da classe da model
     * @param  DataBase\TableSchema $tableSchema Esquema da tabela
     * @param  &array $row         Lista com valores para inicializar a model
     * @param  bool $loaded      Flag $__loaded__
     * @return DataBase\ModelBase              Nova model
     */
    protected static function getModelInstance($modelName, $tableSchema, &$row = null, $loaded = true){

        $model = new $modelName;

        foreach ($row as $name => $value){
            $propertyName = null;
            $columnSchema = $tableSchema->getColumnByName($name, $propertyName);
            $model->_set($propertyName, $value, $columnSchema);
        }

        $model->_setLoaded($loaded);
        return $model;
    }

    /**
     * Converte a model em um array
     * @return array
     */
    public function toArray(){
        $modelName = get_called_class();
        $tableSchema = self::getTableSchema($modelName);
        $schemaCols = $tableSchema->getColumns();
        $data = array();

        foreach ($schemaCols as $property => $schema)
            $data[$property] = $this->_get($property);

        return $data;
    }

    /**
     * Setter generico da model
     * @param string $property     Nome da propriedade
     * @param mixin $value        Valor que será definido a propriedade
     * @param array $columnSchema Esquema da coluna
     */
    protected function _set($property, $value, $columnSchema){

        //Não faz nada se não existir a propriedade
        if(!property_exists($this, $property)) return;

        //Utiliza o metodo setter caso exista
        if(method_exists($this, 'set'.ucfirst($property))){
            $setter = 'set'.ucfirst($property);
            $this->$setter(Types::casting($value, $columnSchema['type']));
            return;
        }

        //Ou apenas tenta atribuir o valor a propriedade
        $this->$property = Types::casting($value, $columnSchema['type']);
    }

    /**
     * Getters generico da model
     * @param  string $property     Nome da propriedade
     * @return mixin           Valor presente na propriedade
     */
    protected function _get($property){

        //Não faz nada se não existir a propriedade
        if(!property_exists($this, $property)) return null;

        //Utiliza o metodo getter caso exista
        if(method_exists($this, 'get'.ucfirst($property))){
            $getter = 'get'.ucfirst($property);
            return $this->$getter();
        }

        //Ou apenas tenta retornar o valor a propriedade
        return $this->$property;
    }

    /**
     * Ativa ou desativa a flag $__loaded__
     */
    protected function _setLoaded($loaded){
        $this->__loaded__ = $loaded;
    }
}
