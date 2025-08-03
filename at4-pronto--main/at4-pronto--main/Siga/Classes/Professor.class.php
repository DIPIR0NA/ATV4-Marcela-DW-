<?php
require_once "Usuario.class.php";

class Professor extends Usuario{
    private $salario;

    public function __construct($id,$nome,$email,$senha,$matricula,$contato){
        parent::__construct($id,$nome,$email,$senha,$matricula,$contato);
        $this->setSalario($salario);
    }

    public function setSalario($salario){
        $this->salario = $salario;
    }


    public function getSalario() {return $this->salario;}

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