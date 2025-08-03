<?php
include "../config/config.inc.php";

class Database{
    private static $conexao = null;

    private static function abrirConexao(){
        if (self::$conexao === null) {
            try{
                self::$conexao = new PDO(DSN, USUARIO, SENHA);
                self::$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $e){
                die("Erro ao conectar com o banco de dados: " . $e->getMessage());
            }
        }
        return self::$conexao;
    }

    public static function getConnection(){
        return self::abrirConexao();
    }

    private static function preparar($sql){
        return self::abrirConexao()->prepare($sql);
    }

    private static function vincularParametros($stmt, $params){
        foreach($params as $key => $value){
            $stmt->bindValue($key, $value);
        }
        return $stmt;
    }

    public static function executar($sql, $params = []){
        $stmt = self::preparar($sql);
        $stmt = self::vincularParametros($stmt, $params);
        $stmt->execute();
        return $stmt;
    }
}
?>
