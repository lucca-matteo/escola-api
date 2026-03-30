<?php

class Aluno {
    private $conn;
    private $table = "alunos";

    public $RA;
    public $Nome;
    public $Email;
    public $DataNascimento;
    public $Turma;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY Nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function buscarPorRA() {
        $query = "SELECT * FROM " . $this->table . " WHERE RA = :RA LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":RA", $this->RA);
        $stmt->execute();
        return $stmt;
    }

    public function criar() {
        $query = "INSERT INTO " . $this->table . " (Nome, Email, `Data de Nascimento`, Turma)
                  VALUES (:Nome, :Email, :DataNascimento, :Turma)";
        $stmt = $this->conn->prepare($query);

        $this->Nome          = htmlspecialchars(strip_tags($this->Nome));
        $this->Email         = htmlspecialchars(strip_tags($this->Email));
        $this->DataNascimento = htmlspecialchars(strip_tags($this->DataNascimento));
        $this->Turma         = htmlspecialchars(strip_tags($this->Turma));

        $stmt->bindParam(":Nome",          $this->Nome);
        $stmt->bindParam(":Email",         $this->Email);
        $stmt->bindParam(":DataNascimento", $this->DataNascimento);
        $stmt->bindParam(":Turma",         $this->Turma);

        if ($stmt->execute()) {
            $this->RA = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function atualizar() {
        $query = "UPDATE " . $this->table . "
                  SET Nome = :Nome, Email = :Email, `Data de Nascimento` = :DataNascimento, Turma = :Turma
                  WHERE RA = :RA";
        $stmt = $this->conn->prepare($query);

        $this->Nome          = htmlspecialchars(strip_tags($this->Nome));
        $this->Email         = htmlspecialchars(strip_tags($this->Email));
        $this->DataNascimento = htmlspecialchars(strip_tags($this->DataNascimento));
        $this->Turma         = htmlspecialchars(strip_tags($this->Turma));
        $this->RA            = htmlspecialchars(strip_tags($this->RA));

        $stmt->bindParam(":Nome",          $this->Nome);
        $stmt->bindParam(":Email",         $this->Email);
        $stmt->bindParam(":DataNascimento", $this->DataNascimento);
        $stmt->bindParam(":Turma",         $this->Turma);
        $stmt->bindParam(":RA",            $this->RA);

        return $stmt->execute();
    }

    public function deletar() {
        $query = "DELETE FROM " . $this->table . " WHERE RA = :RA";
        $stmt = $this->conn->prepare($query);
        $this->RA = htmlspecialchars(strip_tags($this->RA));
        $stmt->bindParam(":RA", $this->RA);
        return $stmt->execute();
    }
}