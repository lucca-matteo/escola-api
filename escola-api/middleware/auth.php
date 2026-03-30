<?php

function verificarAutenticacao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION["usuario_id"])) {
        http_response_code(401);
        echo json_encode(["sucesso" => false, "mensagem" => "Acesso não autorizado. Faça login primeiro."]);
        exit();
    }
}