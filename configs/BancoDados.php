<?php


class Conexao
{
    private static $instancia; 

    private function __construct()
    {
        $hostname = "localhost"; //endereço onde se encontra o banco de dados; localhost corresponde à maquina local
        $database = "clinica_medica_pw2"; //nome do banco de dados
        $username = "root"; //nome de usuário que possui permissão para esse banco de dados específico
        $password = ""; //senha do usuário que possui permissão para esse banco de dados específico

        $dsn = "mysql:host=$hostname;dbname=$database";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //sistema de exceções
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //sistema de busca associativa
            PDO::ATTR_EMULATE_PREPARES => false, 
        ];

        try {
            self::$instancia = new PDO($dsn, $username, $password, $options);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public static function getConexao()
    {
        if (!isset(self::$instancia)) { //se não existir o objeto da conexão, essa funcao cria ele na linha abaixo
            new Conexao();
        }
        return self::$instancia; //se o objeto ja existir, ele o retorna
    }
}
