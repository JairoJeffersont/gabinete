<?php

namespace GabineteDigital\Middleware;

/**
 * Classe responsável por registrar logs em arquivos.
 */
class Logger {
    /**
     * Registra uma nova mensagem de log.
     *
     * @param string $title Título ou identificador do tipo de log.
     * @param string $message Mensagem a ser registrada no log.
     */
    function novoLog($title, $message) {
        $logFile = dirname(__DIR__, 2) . '/logs/' . date('Y_m_d') . '_' . $title . '.log';
        $formattedMessage = date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
        file_put_contents($logFile, $formattedMessage, FILE_APPEND | LOCK_EX);
    }
}
