<?php

namespace GabineteDigital\Middleware;

/**
 * Classe responsável por gerenciar o upload de arquivos.
 */
class UploadFile {
    /**
     * Salva um arquivo em uma pasta especificada.
     *
     * @param string $pasta Caminho da pasta onde o arquivo será salvo.
     * @param array $arquivo Dados do arquivo a ser salvo.
     *
     * @return array Retorna um array com o status do upload e o nome do arquivo salvo.
     */
    public function salvarArquivo($pasta, $arquivo) {
        if (!file_exists($pasta)) {
            mkdir($pasta, 0755, true);
        }

        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid() . '.' . $extensao;
        $caminhoArquivo = $pasta . DIRECTORY_SEPARATOR . $nomeArquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $caminhoArquivo)) {
            return ['status' => 'upload_ok', 'filename' => $nomeArquivo];
        } else {
            return ['status' => 'error'];
        }
    }
}
