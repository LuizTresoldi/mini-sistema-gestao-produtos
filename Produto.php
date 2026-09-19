<?php

require_once 'conexao.php';
require_once 'Fornecedor.php';

class Produto
{
    private $id;
    private $nome;
    private $descricao;
    private $preco;
    private $fornecedor;

    public function __construct($id = null, $nome = '', $descricao = '', $preco = 0, $fornecedor = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->preco = $preco;
        $this->fornecedor = $fornecedor;
    }

    public function getId() { return $this->id; }
    public function getNome() { return $this->nome; }
    public function getDescricao() { return $this->descricao; }
    public function getPreco() { return $this->preco; }
    public function getFornecedor() { return $this->fornecedor; }

    public function setNome($nome) { $this->nome = $nome; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    public function setPreco($preco) { $this->preco = $preco; }
    public function setFornecedor($fornecedor) { $this->fornecedor = $fornecedor; }

    public function getPrecoFormatado()
    {
        return 'R$ ' . number_format($this->preco, 2, ',', '.');
    }

    public function salvar()
    {
        $pdo = Conexao::getInstance();
        $fornecedorId = $this->fornecedor ? $this->fornecedor->getId() : null;

        $sql = 'INSERT INTO produto (nome, descricao, preco, fornecedor_id)
                VALUES (:nome, :descricao, :preco, :fornecedor_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $this->nome,
            ':descricao' => $this->descricao,
            ':preco' => $this->preco,
            ':fornecedor_id' => $fornecedorId
        ]);

        $this->id = $pdo->lastInsertId();
    }

    public function atualizar()
    {
        $pdo = Conexao::getInstance();
        $fornecedorId = $this->fornecedor ? $this->fornecedor->getId() : null;

        $sql = 'UPDATE produto SET nome = :nome, descricao = :descricao,
                preco = :preco, fornecedor_id = :fornecedor_id WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $this->nome,
            ':descricao' => $this->descricao,
            ':preco' => $this->preco,
            ':fornecedor_id' => $fornecedorId,
            ':id' => $this->id
        ]);
    }

    public function excluir()
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('DELETE FROM produto WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }

    public static function buscarPorId($id)
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM produto WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();

        if (!$linha) {
            return null;
        }

        $fornecedor = null;
        if ($linha['fornecedor_id']) {
            $fornecedor = Fornecedor::buscarPorId($linha['fornecedor_id']);
        }

        return new Produto($linha['id'], $linha['nome'], $linha['descricao'], $linha['preco'], $fornecedor);
    }

    public static function listarTodos()
    {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->query('SELECT * FROM produto ORDER BY nome');
        $lista = [];

        foreach ($stmt->fetchAll() as $linha) {
            $fornecedor = null;
            if ($linha['fornecedor_id']) {
                $fornecedor = Fornecedor::buscarPorId($linha['fornecedor_id']);
            }
            $lista[] = new Produto($linha['id'], $linha['nome'], $linha['descricao'], $linha['preco'], $fornecedor);
        }

        return $lista;
    }
}