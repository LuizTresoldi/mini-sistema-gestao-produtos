<?php
require_once 'Produto.php';

$f = new Fornecedor(null, 'Tech Ltda', '12.345.678/0001-90', 'contato@tech.com', '44 99999-0000');
$f->salvar();

$p = new Produto(null, 'Teclado', 'Mecanico ABNT2', 250.00, $f);
$p->salvar();

foreach (Produto::listarTodos() as $prod) {
    echo $prod->getNome() . ' - ' . $prod->getPrecoFormatado() . ' - ';
    echo $prod->getFornecedor() ? $prod->getFornecedor()->getNome() : 'sem fornecedor';
    echo '<br>';
}