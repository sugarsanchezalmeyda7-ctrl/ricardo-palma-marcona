<?php
declare(strict_types=1);

session_start();

$dataDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data';
if (!is_dir($dataDirectory)) {
    mkdir($dataDirectory, 0755, true);
}

$database = new PDO('sqlite:' . $dataDirectory . DIRECTORY_SEPARATOR . 'users.sqlite');
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$database->exec(
    'CREATE TABLE IF NOT EXISTS users (' .
    'id INTEGER PRIMARY KEY AUTOINCREMENT,' .
    'name TEXT NOT NULL,' .
    'email TEXT NOT NULL UNIQUE,' .
    'password TEXT NOT NULL,' .
    'role TEXT NOT NULL DEFAULT "user",' .
    'created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP' .
    ')'
);

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function requireUser(): void
{
    if (!isset($_SESSION['user'])) {
        redirect('login.php');
    }
}
