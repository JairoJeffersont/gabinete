<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include './src/Views/home/home.php';;

$rotas = [
    'home' => './src/Views/home/home.php',
    'login' => './src/Views/login/login.php',
    'sair' => './src/Views/login/sair.php',
    'usuarios' => './src/Views/usuarios/usuarios.php',
    'usuario' => './src/Views/usuarios/editar-usuario.php',
    'orgaos-tipos' => './src/Views/orgaos/orgaos-tipos.php',
    'orgao-tipo' => './src/Views/orgaos/editar-orgao-tipo.php',
    'orgaos' => './src/Views/orgaos/orgaos.php',
    'orgao' => './src/Views/orgaos/editar-orgao.php',
    'pessoas-tipos' => './src/Views/pessoas/pessoas-tipos.php',
    'pessoa-tipo' => './src/Views/pessoas/editar-pessoa-tipo.php',
    'profissoes' => './src/Views/pessoas/profissoes.php',
    'profissao' => './src/Views/pessoas/editar-profissao.php',
    'pessoas' => './src/Views/pessoas/pessoas.php',
    'pessoa' => './src/Views/pessoas/editar-pessoa.php',





];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/404.php';
}
