<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {

    private $usuario;

    public function __construct() {

        $db = (new Database())->getConnection();

        $this->usuario = new Usuario($db);
    }

    public function login() {

        $body = json_decode(file_get_contents("php://input"), true);

        if (
            empty($body['email']) ||
            empty($body['senha'])
        ) {

            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Dados inválidos"
            ]);

            return;
        }

        $this->usuario->Email = $body['email'];

        $usuario = $this->usuario->buscarPorEmail();

        if (!$usuario) {

            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Usuário não encontrado"
            ]);

            return;
        }

        if (!password_verify($body['senha'], $usuario['Senha'])) {

            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Senha incorreta"
            ]);

            return;
        }

        session_start();

        $_SESSION['usuario_id'] = $usuario['ID'];

        echo json_encode([
            "sucesso" => true
        ]);
    }
}
