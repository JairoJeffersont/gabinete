<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include './src/Views/home/home.php';;

$rotas = [
    'home' => './src/Views/home/home.php',
    'login' => './src/Views/login/login.php',
    'sair' => './src/Views/login/sair.php',
    'clientes' => './src/Views/clientes/clientes.php',
    'usuarios' => './src/Views/usuarios/usuarios.php',
    'usuario' => './src/Views/usuarios/editar-usuario.php',
    'cadastro' => './src/Views/cadastro/cadastro.php',
    'orgaos-tipos' => './src/Views/orgaos/orgaos-tipos.php',
    'orgao-tipo' => './src/Views/orgaos/editar-orgao-tipo.php',

    
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/404.php';
}
