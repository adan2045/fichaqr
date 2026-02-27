<?php
// Generador rápido de hashes (borrar después si el profe no lo quiere)
header('Content-Type: text/plain; charset=utf-8');

$p = $_GET['p'] ?? '123456';
echo password_hash($p, PASSWORD_DEFAULT);
