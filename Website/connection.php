<?php
$serverName = "sql-construmateriais.database.windows.net";
$database = "db-construmateriais";
$username = "construmateriais";
$password = "Epsm2024#";

try {
    $conn = new PDO(
        "sqlsrv:server=$serverName;Database=$database",
        $username,
        $password,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Função helper para garantir UTF-8
function garantirUTF8($string) {
    if (!mb_detect_encoding($string, 'UTF-8', true)) {
        return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    }
    return $string;
}