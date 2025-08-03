<?php
session_start();

require_once('../valida_login.php');
require_once("../Classes/Disciplina.class.php");
require_once("../Classes/Professor.class.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $nome = isset($_POST['nome']) ? $_POST['nome'] : "";
    $idProfessor = isset($_POST['idProfessor']) ? $_POST['idProfessor'] : null;

    $acao = isset($_POST['acao']) ? $_POST['acao'] : "";

    $disciplina = new Disciplina($id, $nome, $idProfessor);
    if ($acao == 'salvar') {
        if ($id > 0)
            $resultado = $disciplina->alterar();
        else
            $resultado = $disciplina->inserir();
    } elseif ($acao == 'excluir') {
        $resultado = $disciplina->excluir();
    }

    if ($resultado)
        header("Location: index.php");
    else
        echo "Erro ao salvar dados.";
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $formulario = file_get_contents('form_cad_disciplina.html');

    // Carregar lista de professores
    $professores = Professor::listar();
    $opcoes = "<option value=''>Selecione</option>";
    foreach ($professores as $prof) {
        $opcoes .= "<option value='{$prof->getId()}'>{$prof->getNome()}</option>";
    }

    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $resultado = Disciplina::listar(1, $id);

    if ($resultado) {
        $disciplina = $resultado[0];
        $formulario = str_replace('{id}', $disciplina->getId(), $formulario);
        $formulario = str_replace('{nome}', $disciplina->getNome(), $formulario);

        // Marca o professor selecionado
        $opcoesMarcadas = "<option value=''>Selecione</option>";
        foreach ($professores as $prof) {
            $selected = ($prof->getId() == $disciplina->getIdProfessor()) ? "selected" : "";
            $opcoesMarcadas .= "<option value='{$prof->getId()}' $selected>{$prof->getNome()}</option>";
        }
        $formulario = str_replace('{professores}', $opcoesMarcadas, $formulario);
    } else {
        $formulario = str_replace('{id}', 0, $formulario);
        $formulario = str_replace('{nome}', '', $formulario);
        $formulario = str_replace('{professores}', $opcoes, $formulario);
    }

    print($formulario);
}
?>
