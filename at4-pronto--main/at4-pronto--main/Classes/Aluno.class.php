<?php
require_once("Usuario.class.php");
require_once("Database.class.php");

class Aluno extends Usuario
{
    private $nomeResponsavel;

    public function __construct($id, $nome, $email, $senha, $matricula, $contato, $nomeResponsavel)
    {
        parent::__construct($id, $nome, $email, $senha, $matricula, $contato);
        $this->setNomeResponsavel($nomeResponsavel);
    }

    public function setNomeResponsavel($nomeResponsavel)
    {
        $this->nomeResponsavel = $nomeResponsavel;
    }

    public function getNomeResponsavel()
    {
        return $this->nomeResponsavel;
    }

    public function inserir(): bool
    {
        $sql1 = "INSERT INTO usuario (nome, email, senha, matricula, contato) 
                 VALUES (:nome, :email, :senha, :matricula, :contato)";
        $params1 = [
            ':nome' => $this->getNome(),
            ':email' => $this->getEmail(),
            ':senha' => $this->getSenha(),
            ':matricula' => $this->getMatricula(),
            ':contato' => $this->getContato()
        ];
        $ok = Database::executar($sql1, $params1);
        if ($ok) {
            $id = Database::getConnection()->lastInsertId();
            $sql2 = "INSERT INTO aluno (id, nomeResponsavel) VALUES (:id, :responsavel)";
            $params2 = [
                ':id' => $id,
                ':responsavel' => $this->getNomeResponsavel()
            ];
            return Database::executar($sql2, $params2) !== false;
        }
        return false;
    }

    public function alterar(): bool
    {
        $sql1 = "UPDATE usuario 
                    SET nome = :nome, email = :email, senha = :senha, matricula = :matricula, contato = :contato
                  WHERE id = :id";
        $params1 = [
            ':id' => $this->getId(),
            ':nome' => $this->getNome(),
            ':email' => $this->getEmail(),
            ':senha' => $this->getSenha(),
            ':matricula' => $this->getMatricula(),
            ':contato' => $this->getContato()
        ];
        $ok = Database::executar($sql1, $params1);
        if ($ok) {
            $sql2 = "UPDATE aluno SET nomeResponsavel = :responsavel WHERE id = :id";
            $params2 = [
                ':id' => $this->getId(),
                ':responsavel' => $this->getNomeResponsavel()
            ];
            return Database::executar($sql2, $params2) !== false;
        }
        return false;
    }

    public function excluir(): bool
    {
        $sqlAluno = "DELETE FROM aluno WHERE id = :id";
        $ok = Database::executar($sqlAluno, [':id' => $this->getId()]);
        if ($ok) {
            $sqlUsuario = "DELETE FROM usuario WHERE id = :id";
            return Database::executar($sqlUsuario, [':id' => $this->getId()]) !== false;
        }
        return false;
    }
}
