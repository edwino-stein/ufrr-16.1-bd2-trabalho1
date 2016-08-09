Trabalho 1 de Banco de Dados II

Componentes: Edwino Alberto Lopes Stein;
             Pedro Daniel da Silva Gohl;

********************* Pré-requesistos *********************

 - PHP 5.5 ou superior com os seguites extensões instaladas:
    * intl;
    * mbstring;
    * odbc;
    * PDO;
    * pdo_pgsql;
    * pgsql;
    * Reflection;
    * session;

 - PostgreSQL 9.4 ou superior;

 - Recomendavel a utilização de algum sistema operacional baseado em UNIX;


********************* Ambientes de desenvolvimento/testes *********************

 - Mac OSX 10.11.5, PHP 5.5.30, PostgreSQL 9.4.5;
 - Ubuntu 16.04 LTS, PHP 7.0.8, PostgreSQL 9.4.7;

********************* Configurando o banco de dados *********************

Execute as SQLs presentes no diretorio bd/sqls/ seguindo em ordem da númeração;

OBS.:Os parâmetros de conexão estão definidos no arquivo module/Finanacas/Financas.php,
que já se encontra configurado com os parâmetros do usuário da aplicação no banco
de dados que está definido no arquivo bd/sqls/01-user.sql.


********************* Executando a aplicação *********************

Basta definir o diretorio public/ como docmentRoot do servidor HTTP.
Após configurar o seridor HTTP, basta abrir seu navegador e acessar a aplicação.

Exemplo de utilização do servidor embutido do PHP:

    $ cd public/
    $ php -S localhost:8080
