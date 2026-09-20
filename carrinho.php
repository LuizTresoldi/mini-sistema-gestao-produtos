<?php

require_once 'auth.php';
require_once 'Cesta.php';

$cesta = Cesta::obterCestaAtiva($_SESSION['usuario_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produtoId = $_POST['remover'] ?? '';

    if ($produtoId !== '') {
        $cesta->removerProduto($produtoId);
        $cesta->carregarProdutos();
    }
}

$produtos = $cesta->getProdutos();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrinho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand navbar-dark bg-secondary">
    <div class="container">
        <span class="navbar-brand">Gestão de Produtos</span>
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="produtos.php">Produtos</a></li>
            <li class="nav-item"><a class="nav-link" href="cadastros.php">Cadastros</a></li>
            <li class="nav-item"><a class="nav-link" href="atualizar.php">Atualizar</a></li>
            <li class="nav-item"><a class="nav-link active" href="carrinho.php">Carrinho</a></li>
        </ul>
        <a class="nav-link text-danger" href="logout.php">Sair</a>
    </div>
</nav>

<div class="container py-4">

    <h1 class="h4 mb-4">Carrinho</h1>

    <?php if (count($produtos) === 0): ?>

        <div class="alert alert-info">
            Sua cesta está vazia. Vá em <a href="produtos.php">Produtos</a> para selecionar itens.
        </div>

    <?php else: ?>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Fornecedor</th>
                            <th>Preço</th>
                            <th style="width: 100px">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?= htmlspecialchars($produto->getNome()) ?></td>
                                <td><?= htmlspecialchars($produto->getDescricao()) ?></td>
                                <td>
                                    <?= $produto->getFornecedor()
                                        ? htmlspecialchars($produto->getFornecedor()->getNome())
                                        : '-' ?>
                                </td>
                                <td><?= $produto->getPrecoFormatado() ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="remover" value="<?= $produto->getId() ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Usuário</p>
                        <p class="h5 mb-0"><?= htmlspecialchars($cesta->getUsuario()->getNome()) ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Produtos</p>
                        <p class="h5 mb-0"><?= $cesta->getQuantidade() ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Valor total</p>
                        <p class="h5 mb-0"><?= $cesta->getTotalFormatado() ?></p>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

</body>
</html>