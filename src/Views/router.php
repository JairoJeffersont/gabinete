<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include './src/Views/home/home.php';;

$rotas = [
    'home' => './src/Views/home/home.php',
    'login' => './src/Views/login/login.php',
    'sair' => './src/Views/login/sair.php',
    'usuarios' => './src/Views/usuarios/usuarios.php',
    'usuario' => './src/Views/usuarios/editar-usuario.php',



];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/404.php';
}
