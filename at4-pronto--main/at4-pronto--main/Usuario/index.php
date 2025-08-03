<?php
session_start();
require_once('../valida_login.php');
require_once("../Classes/Usuario.class.php");
require_once("../Classes/Aluno.class.php");
require_once("../Classes/Professor.class.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id         = $_POST['id'] ?? 0;
    $nome       = $_POST['nome'] ?? '';
    $email      = $_POST['email'] ?? '';
    $senha      = $_POST['senha'] ?? '';
    $matricula  = $_POST['matricula'] ?? '';
    $contato    = $_POST['contato'] ?? '';
    $tipo       = $_POST['tipo'] ?? '';
    $acao       = $_POST['acao'] ?? '';

    if ($tipo == 'professor') {
        $salario = $_POST['salario'] ?? 0;
        $usuario = new Professor($id, $nome, $email, $senha, $matricula, $contato, $salario);
    } else {
        $nomeResponsavel = $_POST['nomeResponsavel'] ?? '';
        $usuario = new Aluno($id, $nome, $email, $senha, $matricula, $contato, $nomeResponsavel);
    }

    if ($acao == 'salvar') {
        $resultado = ($id > 0) ? $usuario->alterar() : $usuario->inserir();
    } elseif ($acao == 'excluir') {
        $resultado = $usuario->excluir();
    }

    if ($resultado)
        header("Location: lista_usuario.php");
    else
        echo "Erro ao salvar dados!";
}
elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $form = file_get_contents("form_cad_usuario.html");

    $id = $_GET['id'] ?? 0;
    $usuario = Usuario::listar(1, $id)[0] ?? null;

    $form = str_replace('{id}', $usuario ? $usuario->getId() : 0, $form);
    $form = str_replace('{nome}', $usuario?->getNome() ?? '', $form);
    $form = str_replace('{email}', $usuario?->getEmail() ?? '', $form);
    $form = str_replace('{senha}', $usuario?->getSenha() ?? '', $form);
    $form = str_replace('{matricula}', $usuario?->getMatricula() ?? '', $form);
    $form = str_replace('{contato}', $usuario?->getContato() ?? '', $form);

    if ($usuario instanceof Professor) {
        $form = str_replace('{salario}', $usuario->getSalario(), $form);
        $form = str_replace('{nomeResponsavel}', '', $form);
        $form = str_replace('{professorSelecionado}', 'checked', $form);
        $form = str_replace('{alunoSelecionado}', '', $form);
    } elseif ($usuario instanceof Aluno) {
        $form = str_replace('{salario}', '', $form);
        $form = str_replace('{nomeResponsavel}', $usuario->getNomeResponsavel(), $form);
        $form = str_replace('{professorSelecionado}', '', $form);
        $form = str_replace('{alunoSelecionado}', 'checked', $form);
    } else {
        $form = str_replace('{salario}', '', $form);
        $form = str_replace('{nomeResponsavel}', '', $form);
        $form = str_replace('{professorSelecionado}', '', $form);
        $form = str_replace('{alunoSelecionado}', '', $form);
    }

    print($form);
}
?>
