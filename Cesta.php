<?php

require_once 'conexao.php';
require_once 'Usuario.php';
require_once 'Produto.php';

class Cesta
{
    private $id;
    private $nome;
    private $usuario;
    private $produtos;

    public function __construct($id = null, $nome = '', $usuario = null, $produtos = [])
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->usuario = $usuario;
        $this->produtos = $produtos;
    }

    public function getId() { return $this->id; }
    public function getNome() { return $this->nome; }
    public function getUsuario() { return $this->usuario; }
    public function getProdutos() { return $this->produtos; }

    public function setNome($nome) { $this->nome = $nome; }
    public function setUsuario($usuario) { $this->usuario = $usuario; }

    public function agregarProduto($produto)
    {
        foreach ($this->produtos as $p) {
            if ($p->getId() == $produto->getId()) {
                return false;
            }
        }

        $this->produtos[] = $produto;
        return true;
    }

    public function removerProduto($produtoId)
    {
        $pdo = Conexao::getInstance();
        $sql = 'DELETE FROM cesta_produto WHERE cesta_id = :cesta_id AND produto_id = :produto_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':cesta_id' => $this->id,
            ':produto_id' => $produtoId
        ]);
    }

    public function getQuantidade()
    {
        return count($this->produtos);
    }

    public function getTotal()
    {
        $total = 0;

        foreach ($this->produtos as $produto) {
            $total = $total + $produto->getPreco();
        }

        return $total;
    }

    public function getTotalFormatado()
    {
        return 'R$ ' . number_format($this->getTotal(), 2, ',', '.');
    }

    public function salvar()
    {
        $pdo = Conexao::getInstance();

        if ($this->id === null) {
            $sql = 'INSERT INTO cesta (nome, usuario_id) VALUES (:nome, :usuario_id)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $this->nome,
                ':usuario_id' => $this->usuario->getId()
            ]);

            $this->id = $pdo->lastInsertId();
        }

        foreach ($this->produtos as $produto) {
            $sql = 'INSERT IGNORE INTO cesta_produto (cesta_id, produto_id)
                    VALUES (:cesta_id, :produto_id)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cesta_id' => $this->id,
                ':produto_id' => $produto->getId()
            ]);
        }
    }

    public function excluir()
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('DELETE FROM cesta WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }

    public static function buscarPorId($id)
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM cesta WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();

        if (!$linha) {
            return null;
        }

        $usuario = Usuario::buscarPorId($linha['usuario_id']);

        $cesta = new Cesta($linha['id'], $linha['nome'], $usuario);
        $cesta->carregarProdutos();

        return $cesta;
    }

    public function carregarProdutos()
    {
        $pdo = Conexao::getInstance();
        $sql = 'SELECT produto_id FROM cesta_produto WHERE cesta_id = :cesta_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cesta_id' => $this->id]);

        $this->produtos = [];

        foreach ($stmt->fetchAll() as $linha) {
            $produto = Produto::buscarPorId($linha['produto_id']);

            if ($produto) {
                $this->produtos[] = $produto;
            }
        }
    }

    public static function listarPorUsuario($usuarioId)
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM cesta WHERE usuario_id = :usuario_id ORDER BY id DESC');
        $stmt->execute([':usuario_id' => $usuarioId]);

        $lista = [];
        $usuario = Usuario::buscarPorId($usuarioId);

        foreach ($stmt->fetchAll() as $linha) {
            $cesta = new Cesta($linha['id'], $linha['nome'], $usuario);
            $cesta->carregarProdutos();
            $lista[] = $cesta;
        }

        return $lista;
    }

    public static function obterCestaAtiva($usuarioId)
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM cesta WHERE usuario_id = :usuario_id ORDER BY id DESC LIMIT 1');
        $stmt->execute([':usuario_id' => $usuarioId]);
        $linha = $stmt->fetch();

        if ($linha) {
            return self::buscarPorId($linha['id']);
        }

        $usuario = Usuario::buscarPorId($usuarioId);
        $cesta = new Cesta(null, 'Minha cesta', $usuario);
        $cesta->salvar();

        return $cesta;
    }
}