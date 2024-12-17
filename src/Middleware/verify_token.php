<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;

$config = require dirname(__DIR__, 2) . '/src/Configs/config.php';

$headers = getallheaders();
$token = $headers['Authorization-Info'] ?? '';

if (strpos($token, 'Bearer ') === 0) {
    $token = substr($token, 7);
}

if (empty($token)) {
    $resposta = ['status' => 'forbidden', 'status_code' => 403, 'message' => 'Token não fornecido.'];
} else {
    try {
        $decoded = JWT::decode($token, new Key($config['app']['token_key'], 'HS256'));
        $decoded_array = (array) $decoded;
        return true;
    } catch (ExpiredException $e) {
        $resposta = ['status' => 'forbidden', 'status_code' => 403, 'message' => 'Token expirado.'];
    } catch (SignatureInvalidException $e) {
        $resposta = ['status' => 'forbidden', 'status_code' => 403, 'message' => 'Assinatura inválida.'];
    } catch (Exception $e) {
        $resposta = ['status' => 'forbidden', 'status_code' => 403, 'message' => 'Token mal formado ou inválido.'];
    }
}

header('Content-Type: application/json');
http_response_code($resposta['status_code'] ?? 200);
unset($resposta['status_code']);
echo json_encode($resposta);
exit;