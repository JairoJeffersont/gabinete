<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include '../src/Views/home/home.php';;

$rotas = [
    'home' => '../src/Views/home/home.php',
    'usuarios' => '../src/Views/usuarios/usuarios.php',
    'usuario' => '../src/Views/usuarios/editar-usuario.php',
    'login' => '../src/Views/login/login.php',
    'sair' => '../src/Views/login/sair.php',
    'error' => '../src/Views/error.php',
    'cadastro' => '../src/Views/cadastro/cadastro.php',
    'orgaos' => '../src/Views/orgaos/orgaos.php',
    'orgao' => '../src/Views/orgaos/editar-orgao.php',
    'ficha-orgao' => '../src/Views/orgaos/ficha-orgao.php',
    'orgaos-tipos' => '../src/Views/orgaos/orgaos-tipos.php',
    'orgao-tipo' => '../src/Views/orgaos/editar-orgao-tipo.php',
    'pessoas' => '../src/Views/pessoas/pessoas.php',
    'pessoa' => '../src/Views/pessoas/editar-pessoa.php',
    'pessoas-tipos' => '../src/Views/pessoas/pessoas-tipos.php',
    'pessoa-tipo' => '../src/Views/pessoas/editar-pessoa-tipo.php',
    'profissoes' => '../src/Views/pessoas/profissoes.php',
    'profissao' => '../src/Views/pessoas/editar-profissao.php',
    'nota' => '../src/Views/notas/nota.php',
    'imprimir-nota' => '../src/Views/notas/imprimir-nota.php',
    'oficios' => '../src/Views/oficios/oficios.php',
    'oficio' => '../src/Views/oficios/editar-oficio.php',
    'proposicoes' => '../src/Views/proposicoes/proposicoes.php',
    'proposicao' => '../src/Views/proposicoes/proposicao.php',
    'atualizar-proposicoes' => '../src/Views/proposicoes/atualizar-proposicoes.php',
    'imprimir-proposicoes' => '../src/Views/proposicoes/imprimir-proposicoes.php',
    'postagens-status' => '../src/Views/postagens/postagens-status.php',
    'postagem-status' => '../src/Views/postagens/editar-postagem-status.php',
    'postagens' => '../src/Views/postagens/postagens.php',
    'postagem' => '../src/Views/postagens/postagem.php'

];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include '../src/Views/404.php';
}
