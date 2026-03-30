<?php

class Usuario {
    private $conn;
    private $table = "usuarios";

    public $ID;
    public $Nome;
    public $Email;
    public $Senha;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function cadastrar() {
        $query = "INSERT INTO " . $this->table . " (Nome, Email, Senha)
                  VALUES (:Nome, :Email, :Senha)";
        $stmt = $this->conn->prepare($query);

        $this->Nome  = htmlspecialchars(strip_tags($this->Nome));
        $this->Email = htmlspecialchars(strip_tags($this->Email));
        $senhaHash   = password_hash($this->Senha, PASSWORD_BCRYPT);

        $stmt->bindParam(":Nome",  $this->Nome);
        $stmt->bindParam(":Email", $this->Email);
        $stmt->bindParam(":Senha", $senhaHash);

        if ($stmt->execute()) {
            $this->ID = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function buscarPorEmail() {
        $query = "SELECT * FROM " . $this->table . " WHERE Email = :Email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Email", $this->Email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExiste() {
        $query = "SELECT ID FROM " . $this->table . " WHERE Email = :Email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Email", $this->Email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}