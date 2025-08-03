<?php
require_once("Database.class.php");
require_once("Disciplina.class.php");

abstract class Atividade {
    private $id;
    private $descricao;
    private $peso;
    private $anexo;
    private $idDisciplina;
    private $tipo;

    // Construtor da classe
    public function __construct($id, $desc, $peso, $anexo, $idDisciplina) {
        $this->setId($id);
        $this->setDescricao($desc);
        $this->setPeso($peso);
        $this->setAnexo($anexo);
        $this->setIdDisciplina($idDisciplina);
        $this->setTipo(get_class($this)); // 'Prova' ou 'Trabalho'
    }

    // Setters
    public function setDescricao($desc) {
        if ($desc === "") throw new Exception("Erro, a descrição deve ser informada!");
        $this->descricao = $desc;
    }

    public function setId($id) {
        if ($id < 0) throw new Exception("Erro, o ID deve ser maior que 0!");
        $this->id = $id;
    }

    public function setIdDisciplina($idDisciplina) {
        if ($idDisciplina > 0) {
            $this->idDisciplina = $idDisciplina;
        } else {
            throw new Exception("Erro, deve ser informada uma Disciplina válida.");
        }
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function setPeso($peso) {
        if ($peso < 0) throw new Exception("Erro, o peso deve ser maior que 0!");
        $this->peso = $peso;
    }

    public function setAnexo($anexo = '') {
        $this->anexo = $anexo;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getDescricao(): string {
        return $this->descricao;
    }

    public function getPeso(): float {
        return $this->peso;
    }

    public function getAnexo(): string {
        return $this->anexo;
    }

    public function getIdDisciplina(): int {
        return $this->idDisciplina;
    }

    public function getTipo(): string {
        return $this->tipo;
    }

    // Insere os campos genéricos em atividade e retorna o novo ID
    protected function inserirGenerico(): int {
        $sql = "INSERT INTO atividade (descricao, peso, anexo, tipo, idDisciplina)
                VALUES (:descricao, :peso, :anexo, :tipo, :idDisciplina)";
        $params = [
            ':descricao' => $this->getDescricao(),
            ':peso' => $this->getPeso(),
            ':anexo' => $this->getAnexo(),
            ':tipo' => $this->getTipo(),
            ':idDisciplina' => $this->getIdDisciplina()
        ];
        Database::executar($sql, $params);
        return Database::getConnection()->lastInsertId();
    }

    abstract public function inserir(): bool;
    abstract public function alterar(): bool;

    public static function listar($tipo = 0, $info = ''): array {
        $sql = "SELECT a.id, a.descricao, a.peso, a.anexo, a.tipo, a.idDisciplina, d.nome AS disciplina,
                       p.recuperacao, t.equipe
                  FROM atividade a
             LEFT JOIN prova p ON p.id = a.id
             LEFT JOIN trabalho t ON t.id = a.id
             INNER JOIN disciplina d ON d.id = a.idDisciplina";

        if ($tipo === 1) {
            $sql .= " WHERE a.id = :info";
            $info = (int)$info;
        } elseif ($tipo === 2) {
            $sql .= " WHERE a.descricao LIKE :info";
            $info = '%' . $info . '%';
        }
        $sql .= " ORDER BY a.id";

        $params = ($tipo > 0) ? [':info' => $info] : [];
        $stmt = Database::executar($sql, $params);
        $atividades = [];

        while ($row = $stmt->fetch()) {
            if ($row['tipo'] === 'Prova') {
                $atividades[] = new Prova(
                    $row['id'],
                    $row['descricao'],
                    $row['peso'],
                    $row['anexo'],
                    $row['recuperacao'],
                    $row['idDisciplina']
                );
            } else {
                $atividades[] = new Trabalho(
                    $row['id'],
                    $row['descricao'],
                    $row['peso'],
                    $row['anexo'],
                    $row['equipe'],
                    $row['idDisciplina']
                );
            }
        }
        return $atividades;
    }

    // Método atualizado para garantir exclusão segura das dependências
    public function excluir(): bool {
        $id = $this->getId();

        // Passo 1: Excluir registros na tabela 'prova' que referenciam esta atividade
        $sqlProva = "DELETE FROM prova WHERE id = :id";
        Database::executar($sqlProva, [':id' => $id]);

        // Passo 2: Excluir registros na tabela 'trabalho' (se aplicável)
        $sqlTrabalho = "DELETE FROM trabalho WHERE id = :id";
        Database::executar($sqlTrabalho, [':id' => $id]);

        // Passo 3: Agora excluir a própria atividade
        $sqlAtividade = "DELETE FROM atividade WHERE id = :id";
        return Database::executar($sqlAtividade, [':id' => $id]) !== false;
    }
}
?>