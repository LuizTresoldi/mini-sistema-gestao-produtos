<?php
require_once 'auth.php';
echo '<h2>Login funcionou</h2>';
echo '<p>Usuário logado: ' . htmlspecialchars($_SESSION['usuario_nome']) . '</p>';
echo '<a href="logout.php">Sair</a>';