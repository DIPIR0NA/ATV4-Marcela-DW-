<?php
session_start();

require_once('../valida_login.php');
require_once("../Classes/Usuario.class.php");

$busca = isset($_GET['busca']) ? $_GET['busca'] : 0;
$tipo  = isset($_GET['tipo']) ? $_GET['tipo'] : 0;

$lista = Usuario::listar($tipo, $busca);

$itens = '';

foreach ($lista as $usuario) {
    $item = file_get_contents('itens_listagem_usuarios.html');

    
    $link = '<a href="index.php?id=' . $usuario->getId() . '">' . $usuario->getId() . '</a>';
    $item = str_replace('{id}', $link, $item);
    
    $item = str_replace('{nome}', $usuario->getNome(), $item);
    $item = str_replace('{email}', $usuario->getEmail(), $item);
    $item = str_replace('{matricula}', $usuario->getMatricula(), $item);
    $item = str_replace('{contato}', $usuario->getContato(), $item);
    $item = str_replace('{tipo}', $usuario->getTipo(), $item);

    
    if ($usuario instanceof Professor) {
        $item = str_replace('{salario}', $usuario->getSalario(), $item);
        $item = str_replace('{nomeResponsavel}', '-', $item);
    } elseif ($usuario instanceof Aluno) {
        $item = str_replace('{salario}', '-', $item);
        $item = str_replace('{nomeResponsavel}', $usuario->getNomeResponsavel(), $item);
    } else {
        $item = str_replace('{salario}', '-', $item);
        $item = str_replace('{nomeResponsavel}', '-', $item);
    }

    $itens .= $item;
}

$listagem = file_get_contents('listagem_usuario.html');
$listagem = str_replace('{itens}', $itens, $listagem);
print($listagem);
?>
