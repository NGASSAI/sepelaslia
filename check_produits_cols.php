<?php
require_once 'config/db.php';
$cols = $pdo->query("DESCRIBE produits")->fetchAll(PDO::FETCH_COLUMN);
echo implode("\n", $cols);

