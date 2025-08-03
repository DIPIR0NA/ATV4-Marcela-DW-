<?php
require_once "Usuario.class.php";

class Aluno extends Usuario{
    private $nomeResponsavel;

    public function __construct($id,$nome,$email,$senha,$matricula,$contato){
        parent::__construct($id,$nome,$email,$senha,$matricula,$contato);
        $this->setNomeResponsavel($nomeResponsavel);
    }

    public function setNomeResponsavel($nomeResponsavel){
        $this->nomeResponsavel = $nomeResponsavel;
    }


    public function getNomeResponsavel() {return $this->nomeResponsavel;}

    // sobrescrita de método 
    public function inserir():Bool{
            // montar o sql/ query
            $sql = "INSERT INTO usuario 
                        (nome, email, senha, matricula, contato)
                        VALUES(:nome, :email, :senha, :matricula, :contato)";
            
            $parametros = array(':nome'=>$this->getNome(),
                                ':email'=>$this->getEmail(),
                                ':senha'=>$this->getSenha(),
                                ':matricula' => $this->getMatricula(),
                                ':contato' => $this->getContato());
            
            return Database::executar($sql, $parametros) == true;
    }

    public function alterar():Bool{       
        $sql = "UPDATE usuario
                   SET nome = :nome, 
                       email = :email,
                       senha = :senha,
                       matricula = :matricula,
                       tipo = :tipo,
                       contato = :contato
                 WHERE id = :id";
          $parametros = array(':id'=>$this->getid(),
                         ':nome'=>$this->getNome(),
                         ':email'=>$this->getEmail(),
                         ':senha'=>$this->getSenha(),
                         ':matricula' => $this->getMatricula(),
                         ':contato' => $this->getContato());
         return Database::executar($sql, $parametros) == true;
     }



   

}