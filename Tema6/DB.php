<?php

class DBProyecto
{
    private $host = 'localhost';
    private $dbName = 'marzo2025';
    private $user = 'root';
    private $pass = '';

    public function getConnection()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $this->user, $this->pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }
}
