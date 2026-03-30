<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {

    private $db;
    private $usuario;

    public function __construct() {
        $database       = new Database();
        $this->db       = $database->getConnection();
        $this->usuario  = new Usuario($this->db);
    }

    public function cadastro() {
        $body = json_decode(file_get_contents("php://input"), true);

        if (empty($body["nome"]) || empty($body["email"]) || empty($body["senha"])) {
            http_response_code(400);
            echo json_encode(["sucesso" => false, "mensagem" => "Campos obrigatórios: nome, email, senha."]);
            return;
        }

        if (!filter_var($body["email"], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["sucesso" => false, "mensagem" => "E-mail inválido."]);
            return;
        }

        if (strlen($body["senha"]) < 6) {
            http_response_code(400);
            echo json_encode(["sucesso" => false, "mensagem" => "A senha deve ter pelo menos 6 caracteres."]);
            return;
        }

        $this->usuario->Email = $body["email"];
        if ($this->usuario->emailExiste()) {
            http_response_code(409);
            echo json_encode(["sucesso" => false, "mensagem" => "Este e-mail já está cadastrado."]);
            return;
        }

        $this->usuario->Nome  = $body["nome"];
        $this->usuario->Senha = $body["senha"];

        if ($this->usuario->cadastrar()) {
            http_response_code(201);
            echo json_encode([
                "sucesso"  => true,
                "mensagem" => "Usuário cadastrado com sucesso.",
                "id"       => $this->usuario->ID
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["sucesso" => false, "mensagem" => "Erro ao cadastrar usuário."]);
        }
    }

    public function login() {
        $body = json_decode(file_get_contents("php://input"), true);

        if (empty($body["email"]) || empty($body["senha"])) {
            http_response_code(400);
            echo json_encode(["sucesso" => false, "mensagem" => "Campos obrigatórios: email, senha."]);
            return;
        }

        $this->usuario->Email = $body["email"];
        $usuario = $this->usuario->buscarPorEmail();

        if (!$usuario) {
            http_response_code(401);
            echo json_encode(["sucesso" => false, "mensagem" => "E-mail ou senha incorretos."]);
            return;
        }

        if (!password_verify($body["senha"], $usuario["Senha"])) {
            http_response_code(401);
            echo json_encode(["sucesso" => false, "mensagem" => "E-mail ou senha incorretos."]);
            return;
        }

        session_start();
        $_SESSION["usuario_id"]    = $usuario["ID"];
        $_SESSION["usuario_nome"]  = $usuario["Nome"];
        $_SESSION["usuario_email"] = $usuario["Email"];

        http_response_code(200);
        echo json_encode([
            "sucesso"  => true,
            "mensagem" => "Login realizado com sucesso.",
            "usuario"  => [
                "id"    => $usuario["ID"],
                "nome"  => $usuario["Nome"],
                "email" => $usuario["Email"]
            ]
        ]);
    }

    public function logout() {
        session_start();
        session_destroy();
        http_response_code(200);
        echo json_encode(["sucesso" => true, "mensagem" => "Logout realizado com sucesso."]);
    }
}