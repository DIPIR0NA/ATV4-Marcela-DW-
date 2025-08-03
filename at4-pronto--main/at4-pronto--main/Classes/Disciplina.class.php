<?php

require_once "Database.class.php";
require_once "Formulario.interface.php";

class Disciplina implements Formulario
{
    private $id;
    private $nome;
    private $idProfessor;
    private $nomeProfessor;
    private $atividades;

    public function __construct($id, $nome, $idProfessor = null)
    {
        $this->setId($id);
        $this->setNome($nome);
        $this->setIdProfessor($idProfessor);
        $this->atividades = array();
    }

    public function addAtividade(Atividade $atividade)
    {
        array_push($this->atividades, $atividade);
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function setIdProfessor($idProfessor)
    {
        $this->idProfessor = $idProfessor;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getIdProfessor(): ?int
    {
        return $this->idProfessor;
    }

    public function getNomeProfessor(): ?string
    {
        return $this->nomeProfessor;
    }

    public function inserir(): bool
    {
        $sql = "INSERT INTO disciplina 
                    (nome, idProfessor)
                VALUES (:nome, :idProfessor)";
        
        $parametros = array(
            ':nome' => $this->getNome(),
            ':idProfessor' => $this->getIdProfessor()
        );
        
        return Database::executar($sql, $parametros) == true;
    }

    public static function listar($tipo = 0, $info = ''): array
    {
        $sql = "SELECT d.*, p.nome AS nomeProfessor 
                  FROM disciplina d
             LEFT JOIN usuario p ON p.id = d.idProfessor";
        
        switch ($tipo) {
            case 0:
                break;
            case 1:
                $sql .= " WHERE d.id = :info ORDER BY d.id";
                break;
            case 2:
                $sql .= " WHERE d.nome LIKE :info ORDER BY d.nome";
                $info = '%' . $info . '%';
                break;
        }

        $parametros = [];
        if ($tipo > 0) {
            $parametros = [':info' => $info];
        }

        $comando = Database::executar($sql, $parametros);
        $disciplinas = [];

        while ($registro = $comando->fetch()) {
            $disciplina = new Disciplina(
                $registro['id'],
                $registro['nome'],
                $registro['idProfessor']
            );
            $disciplina->nomeProfessor = $registro['nomeProfessor'] ?? null;
            array_push($disciplinas, $disciplina);
        }
        return $disciplinas;
    }

    public function alterar(): bool
    {
        $sql = "UPDATE disciplina
                   SET nome = :nome,
                       idProfessor = :idProfessor
                 WHERE id = :id";
        $parametros = array(
            ':id' => $this->getId(),
            ':nome' => $this->getNome(),
            ':idProfessor' => $this->getIdProfessor()
        );
        return Database::executar($sql, $parametros) == true;
    }

    public function excluir(): bool
    {
        // Passo 1: Excluir registros em 'prova' que referenciam atividades desta disciplina
        $sqlProva = "
            DELETE p 
              FROM prova p
              JOIN atividade a ON p.id = a.id
             WHERE a.idDisciplina = :idDisciplina";
        $paramsProva = [':idDisciplina' => $this->getId()];
        Database::executar($sqlProva, $paramsProva);

        // Passo 2: Excluir registros em 'trabalho' que referenciam atividades desta disciplina
        $sqlTrabalho = "
            DELETE t 
              FROM trabalho t
              JOIN atividade a ON t.id = a.id
             WHERE a.idDisciplina = :idDisciplina";
        $paramsTrabalho = [':idDisciplina' => $this->getId()];
        Database::executar($sqlTrabalho, $paramsTrabalho);

        // Passo 3: Excluir todas as atividades vinculadas à disciplina
        $sqlAtividade = "DELETE FROM atividade WHERE idDisciplina = :idDisciplina";
        $paramsAtividade = [':idDisciplina' => $this->getId()];
        Database::executar($sqlAtividade, $paramsAtividade);

        // Passo 4: Agora excluir a disciplina
        $sqlDisciplina = "DELETE FROM disciplina WHERE id = :id";
        $paramsDisciplina = [':id' => $this->getId()];
        return Database::executar($sqlDisciplina, $paramsDisciplina) == true;
    }
}