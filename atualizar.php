<?php

require_once 'auth.php';
require_once 'Produto.php';
require_once 'Fornecedor.php';
require_once 'Cesta.php';

$produtos = Produto::listarTodos();
$fornecedores = Fornecedor::listarTodos();
$cestas = Cesta::listarPorUsuario($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atualizar dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand navbar-dark bg-secondary">
    <div class="container">
        <span class="navbar-brand">Gestão de Produtos</span>
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="produtos.php">Produtos</a></li>
            <li class="nav-item"><a class="nav-link" href="cadastros.php">Cadastros</a></li>
            <li class="nav-item"><a class="nav-link active" href="atualizar.php">Atualizar</a></li>
            <li class="nav-item"><a class="nav-link" href="carrinho.php">Carrinho</a></li>
        </ul>
        <a class="nav-link text-danger" href="logout.php">Sair</a>
    </div>
</nav>

<div class="container py-4">

    <h1 class="h4 mb-4">Atualizar dados</h1>

    <div class="row g-3">

        <div class="col-md-5">

            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3">Produtos</h2>
                    <?php foreach ($produtos as $p): ?>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span><?= htmlspecialchars($p->getNome()) ?></span>
                            <a href="#" onclick="carregar('produto', <?= $p->getId() ?>); return false;">Editar</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3">Fornecedores</h2>
                    <?php foreach ($fornecedores as $f): ?>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span><?= htmlspecialchars($f->getNome()) ?></span>
                            <a href="#" onclick="carregar('fornecedor', <?= $f->getId() ?>); return false;">Editar</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="h6 mb-3">Cestas</h2>
                    <?php foreach ($cestas as $c): ?>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span><?= htmlspecialchars($c->getNome()) ?></span>
                            <a href="#" onclick="carregar('cesta', <?= $c->getId() ?>); return false;">Editar</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6 mb-3" id="tituloEdicao">Selecione um item para editar</h2>

                    <div id="areaEdicao" class="d-none">

                        <input type="hidden" id="tipo">
                        <input type="hidden" id="id">

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome">
                        </div>

                        <div class="mb-3 campo-produto d-none">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control" id="descricao">
                        </div>

                        <div class="mb-3 campo-produto d-none">
                            <label class="form-label">Preço</label>
                            <input type="number" step="0.01" class="form-control" id="preco">
                        </div>

                        <div class="mb-3 campo-produto d-none">
                            <label class="form-label">Fornecedor</label>
                            <select class="form-select" id="fornecedor_id">
                                <option value="">Selecione</option>
                                <?php foreach ($fornecedores as $f): ?>
                                    <option value="<?= $f->getId() ?>"><?= htmlspecialchars($f->getNome()) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3 campo-fornecedor d-none">
                            <label class="form-label">CNPJ</label>
                            <input type="text" class="form-control" id="cnpj">
                        </div>

                        <div class="mb-3 campo-fornecedor d-none">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email">
                        </div>

                        <div class="mb-3 campo-fornecedor d-none">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone">
                        </div>

                        <button class="btn btn-primary" onclick="atualizar()">Atualizar</button>

                        <p id="retorno" class="mt-3 mb-0"></p>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
function carregar(tipo, id) {
    var requisicao = new XMLHttpRequest();
    requisicao.open('POST', 'ajax.php', true);
    requisicao.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    requisicao.onreadystatechange = function () {
        if (requisicao.readyState === 4 && requisicao.status === 200) {
            var resposta = JSON.parse(requisicao.responseText);

            if (!resposta.ok) {
                document.getElementById('retorno').innerHTML = resposta.erro;
                return;
            }

            preencher(tipo, resposta.dados);
        }
    };

    requisicao.send('acao=buscar&tipo=' + tipo + '&id=' + id);
}

function preencher(tipo, dados) {
    document.getElementById('areaEdicao').classList.remove('d-none');
    document.getElementById('retorno').innerHTML = '';

    document.getElementById('tipo').value = tipo;
    document.getElementById('id').value = dados.id;
    document.getElementById('nome').value = dados.nome;

    var titulos = {
        produto: 'Editando produto',
        fornecedor: 'Editando fornecedor',
        cesta: 'Editando cesta'
    };
    document.getElementById('tituloEdicao').innerHTML = titulos[tipo];

    var camposProduto = document.querySelectorAll('.campo-produto');
    var camposFornecedor = document.querySelectorAll('.campo-fornecedor');

    for (var i = 0; i < camposProduto.length; i++) {
        camposProduto[i].classList.add('d-none');
    }
    for (var i = 0; i < camposFornecedor.length; i++) {
        camposFornecedor[i].classList.add('d-none');
    }

    if (tipo === 'produto') {
        for (var i = 0; i < camposProduto.length; i++) {
            camposProduto[i].classList.remove('d-none');
        }
        document.getElementById('descricao').value = dados.descricao;
        document.getElementById('preco').value = dados.preco;
        document.getElementById('fornecedor_id').value = dados.fornecedor_id;
    }

    if (tipo === 'fornecedor') {
        for (var i = 0; i < camposFornecedor.length; i++) {
            camposFornecedor[i].classList.remove('d-none');
        }
        document.getElementById('cnpj').value = dados.cnpj;
        document.getElementById('email').value = dados.email;
        document.getElementById('telefone').value = dados.telefone;
    }
}

function atualizar() {
    var tipo = document.getElementById('tipo').value;
    var id = document.getElementById('id').value;
    var nome = document.getElementById('nome').value;
    var retorno = document.getElementById('retorno');

    if (nome.trim() === '') {
        retorno.innerHTML = 'Informe o nome.';
        retorno.className = 'mt-3 mb-0 text-danger';
        return;
    }

    var dados = 'acao=atualizar&tipo=' + tipo + '&id=' + id;
    dados = dados + '&nome=' + encodeURIComponent(nome);

    if (tipo === 'produto') {
        dados = dados + '&descricao=' + encodeURIComponent(document.getElementById('descricao').value);
        dados = dados + '&preco=' + document.getElementById('preco').value;
        dados = dados + '&fornecedor_id=' + document.getElementById('fornecedor_id').value;
    }

    if (tipo === 'fornecedor') {
        dados = dados + '&cnpj=' + encodeURIComponent(document.getElementById('cnpj').value);
        dados = dados + '&email=' + encodeURIComponent(document.getElementById('email').value);
        dados = dados + '&telefone=' + encodeURIComponent(document.getElementById('telefone').value);
    }

    var requisicao = new XMLHttpRequest();
    requisicao.open('POST', 'ajax.php', true);
    requisicao.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    requisicao.onreadystatechange = function () {
        if (requisicao.readyState === 4 && requisicao.status === 200) {
            var resposta = JSON.parse(requisicao.responseText);

            if (resposta.ok) {
                retorno.innerHTML = resposta.mensagem;
                retorno.className = 'mt-3 mb-0 text-success';
            } else {
                retorno.innerHTML = resposta.erro;
                retorno.className = 'mt-3 mb-0 text-danger';
            }
        }
    };

    requisicao.send(dados);
}
</script>

</body>
</html>