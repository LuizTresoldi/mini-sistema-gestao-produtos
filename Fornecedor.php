<?php

require_once 'conexao.php';

class Fornecedor
{
    private $id;
    private $nome;
    private $cnpj;
    private $email;
    private $telefone;

    public function __construct($id = null, $nome = '', $cnpj = '', $email = '', $telefone = '')
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->cnpj = $cnpj;
        $this->email = $email;
        $this->telefone = $telefone;
    }

    public function getId() { return $this->id; }
    public function getNome() { return $this->nome; }
    public function getCnpj() { return $this->cnpj; }
    public function getEmail() { return $this->email; }
    public function getTelefone() { return $this->telefone; }

    public function setNome($nome) { $this->nome = $nome; }
    public function setCnpj($cnpj) { $this->cnpj = $cnpj; }
    public function setEmail($email) { $this->email = $email; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }

    public function salvar()
    {
        $pdo = Conexao::getInstance();

        $sql = 'INSERT INTO fornecedor (nome, cnpj, email, telefone)
                VALUES (:nome, :cnpj, :email, :telefone)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $this->nome,
            ':cnpj' => $this->cnpj,
            ':email' => $this->email,
            ':telefone' => $this->telefone
        ]);

        $this->id = $pdo->lastInsertId();
    }

    public function atualizar()
    {
        $pdo = Conexao::getInstance();

        $sql = 'UPDATE fornecedor SET nome = :nome, cnpj = :cnpj,
                email = :email, telefone = :telefone WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $this->nome,
            ':cnpj' => $this->cnpj,
            ':email' => $this->email,
            ':telefone' => $this->telefone,
            ':id' => $this->id
        ]);
    }

    public function excluir()
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('DELETE FROM fornecedor WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }

    public static function buscarPorId($id)
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM fornecedor WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();

        if (!$linha) {
            return null;
        }

        return new Fornecedor($linha['id'], $linha['nome'], $linha['cnpj'], $linha['email'], $linha['telefone']);
    }

    public static function listarTodos()
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->query('SELECT * FROM fornecedor ORDER BY nome');
        $lista = [];

        foreach ($stmt->fetchAll() as $linha) {
            $lista[] = new Fornecedor($linha['id'], $linha['nome'], $linha['cnpj'], $linha['email'], $linha['telefone']);
        }

        return $lista;
    }
}