<?php
require_once "Atividade.class.php";

class Prova extends Atividade{
    private $recuperacao;

    public function __construct($id, $desc, $peso, $anexo, $recuperacao, $idDisciplina){
        parent::__construct($id, $desc, $peso, $anexo, $idDisciplina);
        $this->setRecuperacao($recuperacao);
    }

    public function setRecuperacao($recuperacao){ $this->recuperacao = $recuperacao; }
    public function getRecuperacao(){ return $this->recuperacao; }

    public function inserir(): bool {
        $idAtv = parent::inserirGenerico();
        $sql2 = "INSERT INTO prova (id, recuperacao) VALUES (:id, :recuperacao)";
        return Database::executar($sql2, [
            ':id'          => $idAtv,
            ':recuperacao' => $this->getRecuperacao()
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
        // Atualiza prova
        $sql2 = "UPDATE prova SET recuperacao = :recuperacao WHERE id = :id";
        return Database::executar($sql2, [
            ':id'           => $this->getId(),
            ':recuperacao'  => $this->getRecuperacao()
        ]) !== false;
    }
}
