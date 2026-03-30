<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Aluno.php';

class AlunoController {

    private $db;
    private $aluno;

    public function __construct() {
        $database    = new Database();
        $this->db    = $database->getConnection();
        $this->aluno = new Aluno($this->db);
    }

    public function listar() {
        $stmt    = $this->aluno->listar();
        $alunos  = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $alunos[] = [
                "RA"             => $row["RA"],
                "nome"           => $row["Nome"],
                "email"          => $row["Email"],
                "dataNascimento" => $row["Data de Nascimento"],
                "turma"          => $row["Turma"]
            ];
        }

        http_response_code(200);
        echo json_encode(["sucesso" => true, "dados" => $alunos, "total" => count($alunos)]);
    }

    public function buscar($ra) {
        $this->aluno->RA = $ra;
        $stmt = $this->aluno->buscarPorRA();
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo json_encode(["sucesso" => false, "mensagem" => "Aluno não encontrado."]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "sucesso" => true,
            "dados"   => [
                "RA"             => $row["RA"],
                "nome"           => $row["Nome"],
                "email"          => $row["Email"],
                "dataNascimento" => $row["Data de Nascimento"],
                "turma"          => $row["Turma"]
            ]
        ]);
    }

    public function criar() {
        $body = json_decode(file_get_contents("php://input"), true);

        if (
            empty($body["nome"]) ||
            empty($body["email"]) ||
            empty($body["dataNascimento"]) ||
            empty($body["turma"])
        ) {
            http_response_code(400);
            echo json_encode(["sucesso" => false, "mensagem" => "Campos obrigatórios: nome, email, dataNascimento, turma."]);
            return;
        }

        $this->aluno->Nome           = $body["nome"];
        $this->aluno->Email          = $body["email"];
        $this->aluno->DataNascimento = $body["dataNascimento"];
        $this->aluno->Turma          = $body["turma"];

        if ($this->aluno->criar()) {
            http_response_code(201);
            echo json_encode(["sucesso" => true, "mensagem" => "Aluno cadastrado com sucesso.", "RA" => $this->aluno->RA]);
        } else {
            http_response_code(500);
            echo json_encode(["sucesso" => false, "mensagem" => "Erro ao cadastrar aluno."]);
        }
    }

    public function atualizar($ra) {
        $body = json_decode(file_get_contents("php://input"), true);

        if (
            empty($body["nome"]) ||
            empty($body["email"]) ||
            empty($body["dataNascimento"]) ||
            empty($body["turma"])
        ) {
            http_response_code(400);
            echo json_encode(["sucesso" => false, "mensagem" => "Campos obrigatórios: nome, email, dataNascimento, turma."]);
            return;
        }

        $this->aluno->RA             = $ra;
        $this->aluno->Nome           = $body["nome"];
        $this->aluno->Email          = $body["email"];
        $this->aluno->DataNascimento = $body["dataNascimento"];
        $this->aluno->Turma          = $body["turma"];

        if ($this->aluno->atualizar()) {
            http_response_code(200);
            echo json_encode(["sucesso" => true, "mensagem" => "Aluno atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar aluno."]);
        }
    }

    public function deletar($ra) {
        $this->aluno->RA = $ra;
        $stmt = $this->aluno->buscarPorRA();

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(["sucesso" => false, "mensagem" => "Aluno não encontrado."]);
            return;
        }

        if ($this->aluno->deletar()) {
            http_response_code(200);
            echo json_encode(["sucesso" => true, "mensagem" => "Aluno removido com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["sucesso" => false, "mensagem" => "Erro ao remover aluno."]);
        }
    }
}