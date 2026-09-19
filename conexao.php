<?php

class Conexao
{
    private const HOST = 'localhost';
    private const USUARIO = 'root';
    private const SENHA = '';
    private const BANCO = 'gestao_produtos';

    private static ?PDO $instancia = null;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instancia === null) {
            self::criarBanco();
            self::conectar();
            self::criarTabelas();
        }

        return self::$instancia;
    }

    private static function criarBanco(): void
    {
        try {
            $pdo = new PDO(
                'mysql:host=' . self::HOST,
                self::USUARIO,
                self::SENHA
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'CREATE DATABASE IF NOT EXISTS ' . self::BANCO
                 . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
            $pdo->exec($sql);
        } catch (PDOException $e) {
            die('Erro ao criar o banco de dados: ' . $e->getMessage());
        }
    }

    private static function conectar(): void
    {
        try {
            self::$instancia = new PDO(
                'mysql:host=' . self::HOST . ';dbname=' . self::BANCO . ';charset=utf8mb4',
                self::USUARIO,
                self::SENHA
            );
            self::$instancia->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instancia->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Erro ao conectar no banco de dados: ' . $e->getMessage());
        }
    }

    private static function criarTabelas(): void
    {
        try {
            self::$instancia->exec('
                CREATE TABLE IF NOT EXISTS usuario (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome VARCHAR(100) NOT NULL,
                    email VARCHAR(150) NOT NULL UNIQUE,
                    senha CHAR(64) NOT NULL,
                    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB
            ');

            self::$instancia->exec('
                CREATE TABLE IF NOT EXISTS fornecedor (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome VARCHAR(100) NOT NULL,
                    cnpj VARCHAR(18),
                    email VARCHAR(150),
                    telefone VARCHAR(20)
                ) ENGINE=InnoDB
            ');

            self::$instancia->exec('
                CREATE TABLE IF NOT EXISTS produto (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome VARCHAR(100) NOT NULL,
                    descricao VARCHAR(255),
                    preco DECIMAL(10,2) NOT NULL DEFAULT 0,
                    fornecedor_id INT,
                    CONSTRAINT fk_produto_fornecedor
                        FOREIGN KEY (fornecedor_id) REFERENCES fornecedor(id)
                        ON DELETE SET NULL
                ) ENGINE=InnoDB
            ');

            self::$instancia->exec('
                CREATE TABLE IF NOT EXISTS cesta (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nome VARCHAR(100) NOT NULL,
                    usuario_id INT NOT NULL,
                    criada_em DATETIME DEFAULT CURRENT_TIMESTAMP,
                    CONSTRAINT fk_cesta_usuario
                        FOREIGN KEY (usuario_id) REFERENCES usuario(id)
                        ON DELETE CASCADE
                ) ENGINE=InnoDB
            ');

            self::$instancia->exec('
                CREATE TABLE IF NOT EXISTS cesta_produto (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cesta_id INT NOT NULL,
                    produto_id INT NOT NULL,
                    CONSTRAINT fk_cesta_produto_cesta
                        FOREIGN KEY (cesta_id) REFERENCES cesta(id)
                        ON DELETE CASCADE,
                    CONSTRAINT fk_cesta_produto_produto
                        FOREIGN KEY (produto_id) REFERENCES produto(id)
                        ON DELETE CASCADE,
                    CONSTRAINT uq_cesta_produto UNIQUE (cesta_id, produto_id)
                ) ENGINE=InnoDB
            ');
        } catch (PDOException $e) {
            die('Erro ao criar as tabelas: ' . $e->getMessage());
        }
    }
}