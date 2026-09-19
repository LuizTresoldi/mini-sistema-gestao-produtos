<?php

require_once 'conexao.php';

class Usuario
{
    public function __construct(
        private ?int $id = null,
        private string $nome = '',
        private string $email = '',
        private string $senha = ''
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setSenha(string $senha): void
    {
        $this->senha = hash('sha256', $senha);
    }

    public function cadastrar(): bool
    {
        $pdo = Conexao::getInstance();

        $stmt = $pdo->prepare('SELECT id FROM usuario WHERE email = :email');
        $stmt->execute([':email' => $this->email]);

        if ($stmt->fetch()) {
            return false;
        }

        $sql = 'INSERT INTO usuario (nome, email, senha)
                VALUES (:nome, :email, :senha)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $this->nome,
            ':email' => $this->email,
            ':senha' => $this->senha,
        ]);

        $this->id = (int) $pdo->lastInsertId();

        return true;
    }

    public static function autenticar(string $email, string $senha): ?Usuario
    {
        $pdo = Conexao::getInstance();

        $sql = 'SELECT id, nome, email FROM usuario
                WHERE email = :email AND senha = :senha';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':senha' => hash('sha256', $senha),
        ]);

        $linha = $stmt->fetch();

        if (!$linha) {
            return null;
        }

        return new Usuario(
            (int) $linha['id'],
            $linha['nome'],
            $linha['email']
        );
    }

    public static function buscarPorId(int $id): ?Usuario
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('SELECT id, nome, email FROM usuario WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();

        if (!$linha) {
            return null;
        }

        return new Usuario((int) $linha['id'], $linha['nome'], $linha['email']);
    }
}