# Mini Sistema de Gestão de Produtos

Sistema web para cadastro de produtos, fornecedores e cestas de compra,
com autenticação de usuários. Desenvolvido para a disciplina de
Desenvolvimento Back-End.

## Integrantes

- Luiz Fernando Tresoldi Miguel, RA 60008139

## Tecnologias

PHP com PDO, MySQL, HTML, CSS, JavaScript e Bootstrap.

## Telas

Protótipo no Figma: https://www.figma.com/design/fp4LiiZBJAsogKmDKaA3w6/Mini-Sistema-de-Gest%C3%A3o-de-Produtos.?node-id=0-1&t=oeXHRHj1fY2r9vDa-1

![Login](docs/login.png)
![Cadastro de usuário](docs/cadastro-usuario.png)
![Cadastros](docs/cadastros.png)
![Produtos](docs/produtos.png)
![Carrinho](docs/carrinho.png)
![Atualização](docs/atualizacao.png)

## Modelagem

![DER](docs/der.png)

## Funcionalidades

- Cadastro de usuários com senha armazenada em hash SHA-256
- Autenticação com sessão e proteção das páginas internas
- Cadastro de produtos, fornecedores e cestas
- Relacionamento entre objetos, cada produto possui um fornecedor e cada cesta pertence a um usuário
- Atualização de produtos, fornecedores e cestas via AJAX, sem recarregar a página
- Listagem de produtos com seleção por checkbox e validação no cliente e no servidor
- Carrinho com resumo de usuário, quantidade de produtos e valor total
- Criação automática do banco de dados e das tabelas na primeira execução

## Como executar

1. Clone o repositório dentro da pasta `htdocs` do XAMPP
2. Inicie o Apache e o MySQL no painel de controle do XAMPP
3. Acesse `http://localhost/mini-sistema-gestao-produtos`
4. O banco de dados e as tabelas são criados automaticamente no primeiro acesso
5. Crie um usuário na tela de cadastro e faça login

## Estrutura do projeto

As classes `Usuario`, `Fornecedor`, `Produto` e `Cesta` concentram as regras de
negócio e o acesso ao banco. A classe `Conexao` utiliza o padrão Singleton para
garantir uma única conexão PDO durante a execução.

A tabela `cesta_produto` possui uma restrição de unicidade no par de chaves
estrangeiras, o que garante que um produto apareça apenas uma vez em cada cesta.