<?php

require_once 'auth.php';
require_once 'Produto.php';
require_once 'Cesta.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selecionados = $_POST['produtos'] ?? [];

    if (empty($selecionados)) {
        $mensagem = 'Selecione ao menos um produto.';
        $tipoMensagem = 'danger';
    } else {
        $cesta = Cesta::obterCestaAtiva($_SESSION['usuario_id']);
        $adicionados = 0;

        foreach ($selecionados as $produtoId) {
            $produto = Produto::buscarPorId($produtoId);

            if ($produto && $cesta->agregarProduto($produto)) {
                $adicionados++;
            }
        }

        $cesta->salvar();

        if ($adicionados > 0) {
            $mensagem = $adicionados . ' produto(s) adicionado(s) à cesta.';
            $tipoMensagem = 'success';
        } else {
            $mensagem = 'Os produtos selecionados já estão na cesta.';
            $tipoMensagem = 'warning';
        }
    }
}

$produtos = Produto::listarTodos();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand navbar-dark bg-secondary">
    <div class="container">
        <span class="navbar-brand">Gestão de Produtos</span>
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link active" href="produtos.php">Produtos</a></li>
            <li class="nav-item"><a class="nav-link" href="cadastros.php">Cadastros</a></li>
            <li class="nav-item"><a class="nav-link" href="atualizar.php">Atualizar</a></li>
            <li class="nav-item"><a class="nav-link" href="carrinho.php">Carrinho</a></li>
        </ul>
        <a class="nav-link text-danger" href="logout.php">Sair</a>
    </div>
</nav>

<div class="container py-4">

    <h1 class="h4 mb-4">Produtos</h1>

    <?php if ($mensagem !== ''): ?>
        <div class="alert alert-<?= $tipoMensagem ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div id="alertaValidacao" class="alert alert-danger d-none">
        Selecione ao menos um produto antes de adicionar à cesta.
    </div>

    <?php if (count($produtos) === 0): ?>
        <div class="alert alert-info">
            Nenhum produto cadastrado. Vá em <a href="cadastros.php">Cadastros</a> para adicionar.
        </div>
    <?php else: ?>

    <form method="post" id="formProdutos">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px"></th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Preço</th>
                            <th>Fornecedor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input"
                                           name="produtos[]" value="<?= $produto->getId() ?>">
                                </td>
                                <td><?= htmlspecialchars($produto->getNome()) ?></td>
                                <td><?= htmlspecialchars($produto->getDescricao()) ?></td>
                                <td><?= $produto->getPrecoFormatado() ?></td>
                                <td>
                                    <?= $produto->getFornecedor()
                                        ? htmlspecialchars($produto->getFornecedor()->getNome())
                                        : '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Adicionar à cesta</button>
    </form>

    <?php endif; ?>
</div>

<script>
document.getElementById('formProdutos').addEventListener('submit', function (evento) {
    var marcados = document.querySelectorAll('input[name="produtos[]"]:checked');
    var alerta = document.getElementById('alertaValidacao');

    if (marcados.length === 0) {
        evento.preventDefault();
        alerta.classList.remove('d-none');
    } else {
        alerta.classList.add('d-none');
    }
});
</script>

</body>
</html>