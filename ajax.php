<?php

require_once 'auth.php';
require_once 'Produto.php';
require_once 'Fornecedor.php';
require_once 'Cesta.php';

header('Content-Type: application/json');

$acao = $_POST['acao'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$id = $_POST['id'] ?? '';

if ($acao === 'buscar') {

    if ($tipo === 'produto') {
        $produto = Produto::buscarPorId($id);

        if (!$produto) {
            echo json_encode(['ok' => false, 'erro' => 'Produto nao encontrado']);
            exit;
        }

        echo json_encode([
            'ok' => true,
            'dados' => [
                'id' => $produto->getId(),
                'nome' => $produto->getNome(),
                'descricao' => $produto->getDescricao(),
                'preco' => $produto->getPreco(),
                'fornecedor_id' => $produto->getFornecedor() ? $produto->getFornecedor()->getId() : ''
            ]
        ]);
        exit;
    }

    if ($tipo === 'fornecedor') {
        $fornecedor = Fornecedor::buscarPorId($id);

        if (!$fornecedor) {
            echo json_encode(['ok' => false, 'erro' => 'Fornecedor nao encontrado']);
            exit;
        }

        echo json_encode([
            'ok' => true,
            'dados' => [
                'id' => $fornecedor->getId(),
                'nome' => $fornecedor->getNome(),
                'cnpj' => $fornecedor->getCnpj(),
                'email' => $fornecedor->getEmail(),
                'telefone' => $fornecedor->getTelefone()
            ]
        ]);
        exit;
    }

    if ($tipo === 'cesta') {
        $cesta = Cesta::buscarPorId($id);

        if (!$cesta) {
            echo json_encode(['ok' => false, 'erro' => 'Cesta nao encontrada']);
            exit;
        }

        echo json_encode([
            'ok' => true,
            'dados' => [
                'id' => $cesta->getId(),
                'nome' => $cesta->getNome()
            ]
        ]);
        exit;
    }
}

if ($acao === 'atualizar') {

    if ($tipo === 'produto') {
        $produto = Produto::buscarPorId($id);

        if (!$produto) {
            echo json_encode(['ok' => false, 'erro' => 'Produto nao encontrado']);
            exit;
        }

        $fornecedor = null;
        if (!empty($_POST['fornecedor_id'])) {
            $fornecedor = Fornecedor::buscarPorId($_POST['fornecedor_id']);
        }

        $produto->setNome(trim($_POST['nome'] ?? ''));
        $produto->setDescricao(trim($_POST['descricao'] ?? ''));
        $produto->setPreco($_POST['preco'] ?? 0);
        $produto->setFornecedor($fornecedor);
        $produto->atualizar();

        echo json_encode(['ok' => true, 'mensagem' => 'Produto atualizado com sucesso']);
        exit;
    }

    if ($tipo === 'fornecedor') {
        $fornecedor = Fornecedor::buscarPorId($id);

        if (!$fornecedor) {
            echo json_encode(['ok' => false, 'erro' => 'Fornecedor nao encontrado']);
            exit;
        }

        $fornecedor->setNome(trim($_POST['nome'] ?? ''));
        $fornecedor->setCnpj(trim($_POST['cnpj'] ?? ''));
        $fornecedor->setEmail(trim($_POST['email'] ?? ''));
        $fornecedor->setTelefone(trim($_POST['telefone'] ?? ''));
        $fornecedor->atualizar();

        echo json_encode(['ok' => true, 'mensagem' => 'Fornecedor atualizado com sucesso']);
        exit;
    }

    if ($tipo === 'cesta') {
        $pdo = Conexao::getInstance();
        $stmt = $pdo->prepare('UPDATE cesta SET nome = :nome WHERE id = :id');
        $stmt->execute([
            ':nome' => trim($_POST['nome'] ?? ''),
            ':id' => $id
        ]);

        echo json_encode(['ok' => true, 'mensagem' => 'Cesta atualizada com sucesso']);
        exit;
    }
}

echo json_encode(['ok' => false, 'erro' => 'Acao invalida']);