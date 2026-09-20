<?php

require_once 'auth.php';
require_once 'Fornecedor.php';
require_once 'Produto.php';
require_once 'Cesta.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'fornecedor') {
        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            $mensagem = 'Informe o nome do fornecedor.';
            $tipoMensagem = 'danger';
        } else {
            $fornecedor = new Fornecedor(
                null,
                $nome,
                trim($_POST['cnpj'] ?? ''),
                trim($_POST['email'] ?? ''),
                trim($_POST['telefone'] ?? '')
            );
            $fornecedor->salvar();

            $mensagem = 'Fornecedor cadastrado com sucesso.';
            $tipoMensagem = 'success';
        }
    }

    if ($acao === 'produto') {
        $nome = trim($_POST['nome'] ?? '');
        $preco = $_POST['preco'] ?? '';

        if ($nome === '' || $preco === '') {
            $mensagem = 'Informe o nome e o preço do produto.';
            $tipoMensagem = 'danger';
        } else {
            $fornecedor = null;

            if (!empty($_POST['fornecedor_id'])) {
                $fornecedor = Fornecedor::buscarPorId($_POST['fornecedor_id']);
            }

            $produto = new Produto(
                null,
                $nome,
                trim($_POST['descricao'] ?? ''),
                $preco,
                $fornecedor
            );
            $produto->salvar();

            $mensagem = 'Produto cadastrado com sucesso.';
            $tipoMensagem = 'success';
        }
    }
    if ($acao === 'cesta') {
        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            $mensagem = 'Informe o nome da cesta.';
            $tipoMensagem = 'danger';
        } else {
            $usuario = Usuario::buscarPorId($_SESSION['usuario_id']);

            $cesta = new Cesta(null, $nome, $usuario);
            $cesta->salvar();

            $mensagem = 'Cesta cadastrada com sucesso.';
            $tipoMensagem = 'success';
        }
    }
}

$fornecedores = Fornecedor::listarTodos();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand navbar-dark bg-secondary">
    <div class="container">
        <span class="navbar-brand">Gestão de Produtos</span>
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="produtos.php">Produtos</a></li>
            <li class="nav-item"><a class="nav-link active" href="cadastros.php">Cadastros</a></li>
            <li class="nav-item"><a class="nav-link" href="atualizar.php">Atualizar</a></li>
            <li class="nav-item"><a class="nav-link" href="carrinho.php">Carrinho</a></li>
        </ul>
        <a class="nav-link text-danger" href="logout.php">Sair</a>
    </div>
</nav>

<div class="container py-4">

    <h1 class="h4 mb-4">Cadastros</h1>

    <?php if ($mensagem !== ''): ?>
        <div class="alert alert-<?= $tipoMensagem ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="row g-3">

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Produto</h2>

                    <form method="post">
                        <input type="hidden" name="acao" value="produto">

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control" name="descricao">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Preço</label>
                            <input type="number" step="0.01" class="form-control" name="preco" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fornecedor</label>
                            <select class="form-select" name="fornecedor_id">
                                <option value="">Selecione</option>
                                <?php foreach ($fornecedores as $f): ?>
                                    <option value="<?= $f->getId() ?>"><?= htmlspecialchars($f->getNome()) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar produto</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Fornecedor</h2>

                    <form method="post">
                        <input type="hidden" name="acao" value="fornecedor">

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">CNPJ</label>
                            <input type="text" class="form-control" name="cnpj">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control" name="email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control" name="telefone">
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar fornecedor</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Cesta</h2>

                    <form method="post">
                        <input type="hidden" name="acao" value="cesta">

                        <div class="mb-3">
                            <label class="form-label">Nome da cesta</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Usuário</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['usuario_nome']) ?>" disabled>
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar cesta</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>