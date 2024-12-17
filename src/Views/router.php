<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include './src/Views/home/home.php';;

$rotas = [
    'home' => './src/Views/home/home.php',
    'login' => './src/Views/login/login.php',
];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include './src/Views/404.php';
}
