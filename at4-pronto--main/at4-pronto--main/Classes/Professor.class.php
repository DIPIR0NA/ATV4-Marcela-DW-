<?php
require_once("Usuario.class.php");
require_once("Database.class.php");

class Professor extends Usuario
{
    private $salario;

    public function __construct($id, $nome, $email, $senha, $matricula, $contato, $salario)
    {
        parent::__construct($id, $nome, $email, $senha, $matricula, $contato);
        $this->setSalario($salario);
    }

    public function setSalario($salario)
    {
        $this->salario = $salario;
    }

    public function getSalario()
    {
        return $this->salario;
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
            $sql2 = "INSERT INTO professor (id, salario) VALUES (:id, :salario)";
            $params2 = [
                ':id' => $id,
                ':salario' => $this->getSalario()
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
            $sql2 = "UPDATE professor SET salario = :salario WHERE id = :id";
            $params2 = [
                ':id' => $this->getId(),
                ':salario' => $this->getSalario()
            ];
            return Database::executar($sql2, $params2) !== false;
        }
        return false;
    }

    public function excluir(): bool
    {
        $sql1 = "DELETE FROM professor WHERE id = :id";
        $ok = Database::executar($sql1, [':id' => $this->getId()]);

        if ($ok) {
            $sql2 = "DELETE FROM usuario WHERE id = :id";
            return Database::executar($sql2, [':id' => $this->getId()]) !== false;
        }
        return false;
    }

    public static function listar($tipo = 0, $info = ''): array
    {
        $sql = "SELECT u.*, p.salario
                FROM usuario u
                JOIN professor p ON p.id = u.id";

        if ($tipo == 1) {
            $sql .= " WHERE u.id = :info";
        } elseif ($tipo == 2) {
            $sql .= " WHERE u.nome LIKE :info";
            $info = "%$info%";
        }

        $params = ($tipo > 0) ? [':info' => $info] : [];

        $stmt = Database::executar($sql, $params);
        $professores = [];
        while ($row = $stmt->fetch()) {
            $professores[] = new Professor(
                $row['id'],
                $row['nome'],
                $row['email'],
                $row['senha'],
                $row['matricula'],
                $row['contato'],
                $row['salario']
            );
        }
        return $professores;
    }
}
?>
