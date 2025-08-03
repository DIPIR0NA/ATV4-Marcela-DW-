<?php
require_once("Aluno.class.php");
require_once("Professor.class.php");
require_once("Database.class.php");

abstract class Usuario {
    private $id, $nome, $email, $senha, $matricula, $contato, $tipo;

    public function __construct($id, $nome, $email, $senha, $matricula, $contato) {
        $this->setId($id);
        $this->setNome($nome);
        $this->setEmail($email);
        $this->setSenha($senha);
        $this->setMatricula($matricula);
        $this->setContato($contato);
        $this->setTipo(get_class($this)); 
    }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNome($nome) { $this->nome = $nome; }
    public function setEmail($email) { $this->email = $email; }
    public function setSenha($senha) { $this->senha = $senha; }
    public function setMatricula($matricula) { $this->matricula = $matricula; }
    public function setContato($contato) { $this->contato = $contato; }
    public function setTipo($tipo) { $this->tipo = $tipo; }

    // Getters
    public function getId() { return $this->id; }
    public function getNome() { return $this->nome; }
    public function getEmail() { return $this->email; }
    public function getSenha() { return $this->senha; }
    public function getMatricula() { return $this->matricula; }
    public function getContato() { return $this->contato; }
    public function getTipo() { return $this->tipo; }

    protected function inserirGenerico(): int {
        $sql = "INSERT INTO usuario (nome, email, senha, matricula, contato)
                VALUES (:nome, :email, :senha, :matricula, :contato)";
        $params = [
            ':nome' => $this->getNome(),
            ':email' => $this->getEmail(),
            ':senha' => $this->getSenha(),
            ':matricula' => $this->getMatricula(),
            ':contato' => $this->getContato()
        ];
        Database::executar($sql, $params);
        return Database::getConnection()->lastInsertId();
    }

    abstract public function inserir(): bool;
    abstract public function alterar(): bool;
    abstract public function excluir(): bool;

    public static function listar($tipo = 0, $info = ""): array
    {
        $sql = "SELECT u.*, a.nomeResponsavel, p.salario
                FROM usuario u
                LEFT JOIN aluno a ON a.id = u.id
                LEFT JOIN professor p ON p.id = u.id";

        if ($tipo == 1) {
            $sql .= " WHERE u.id = :info";
        } elseif ($tipo == 2) {
            $sql .= " WHERE u.nome LIKE :info";
            $info = "%$info%";
        }

        $params = ($tipo > 0) ? [':info' => $info] : [];
        $stmt = Database::executar($sql, $params);

        $usuarios = [];
        while ($row = $stmt->fetch()) {
            if (!is_null($row['salario'])) {
                $usuarios[] = new Professor(
                    $row['id'], $row['nome'], $row['email'], $row['senha'],
                    $row['matricula'], $row['contato'], $row['salario']
                );
            } elseif (!is_null($row['nomeResponsavel'])) {
                $usuarios[] = new Aluno(
                    $row['id'], $row['nome'], $row['email'], $row['senha'],
                    $row['matricula'], $row['contato'], $row['nomeResponsavel']
                );
            }
            
        }
        return $usuarios;
    }
}
?>
