<?php
require_once "Atividade.class.php";

class Trabalho extends Atividade{
    private $equipe;

    public function __construct($id, $desc, $peso, $anexo, $equipe, $idDisciplina){
        parent::__construct($id, $desc, $peso, $anexo, $idDisciplina);
        $this->setEquipe($equipe);
    }

    public function setEquipe($equipe){ $this->equipe = $equipe; }
    public function getEquipe(){ return $this->equipe; }

    public function inserir(): bool {
        $idAtv = parent::inserirGenerico();
        $sql2 = "INSERT INTO trabalho (id, equipe) VALUES (:id, :equipe)";
        return Database::executar($sql2, [
            ':id'     => $idAtv,
            ':equipe' => $this->getEquipe()
        ]) !== false;
    }

    public function alterar(): bool {
        // Atualiza atividade genérico
        $sql = "UPDATE atividade SET descricao = :descricao, peso = :peso, anexo = :anexo, tipo = :tipo, idDisciplina = :idDisciplina WHERE id = :id";
        Database::executar($sql, [
            ':id'           => $this->getId(),
            ':descricao'    => $this->getDescricao(),
            ':peso'         => $this->getPeso(),
            ':anexo'        => $this->getAnexo(),
            ':tipo'         => $this->getTipo(),
            ':idDisciplina' => $this->getIdDisciplina()
        ]);
        // Atualiza trabalho
        $sql2 = "UPDATE trabalho SET equipe = :equipe WHERE id = :id";
        return Database::executar($sql2, [
            ':id'     => $this->getId(),
            ':equipe' => $this->getEquipe()
        ]) !== false;
    }
}