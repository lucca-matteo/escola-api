<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/controllers/AlunoController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/middleware/auth.php';

$uri    = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri    = rtrim($uri, "/");
$method = $_SERVER["REQUEST_METHOD"];

$uri = preg_replace('#^/escola-api#', '', $uri);

if ($uri === "/auth/cadastro" && $method === "POST") {
    $ctrl = new AuthController();
    $ctrl->cadastro();
    exit();
}

if ($uri === "/auth/login" && $method === "POST") {
    $ctrl = new AuthController();
    $ctrl->login();
    exit();
}

if ($uri === "/auth/logout" && $method === "POST") {
    $ctrl = new AuthController();
    $ctrl->logout();
    exit();
}

verificarAutenticacao();

if ($uri === "/alunos" && $method === "GET") {
    $ctrl = new AlunoController();
    $ctrl->listar();
    exit();
}

if ($uri === "/alunos" && $method === "POST") {
    $ctrl = new AlunoController();
    $ctrl->criar();
    exit();
}

if (preg_match('#^/alunos/(\d+)$#', $uri, $matches) && $method === "GET") {
    $ctrl = new AlunoController();
    $ctrl->buscar($matches[1]);
    exit();
}

if (preg_match('#^/alunos/(\d+)$#', $uri, $matches) && $method === "PUT") {
    $ctrl = new AlunoController();
    $ctrl->atualizar($matches[1]);
    exit();
}

if (preg_match('#^/alunos/(\d+)$#', $uri, $matches) && $method === "DELETE") {
    $ctrl = new AlunoController();
    $ctrl->deletar($matches[1]);
    exit();
}

http_response_code(404);
echo json_encode(["sucesso" => false, "mensagem" => "Rota não encontrada."]);