<?php

namespace App\Infrastructure;

use PDO;
use Throwable;

final class Database {
    private PDO $pdo;

    public function __construct(
        string $dsn,
        string $user = '',
        string $password = ''
    ) {
        $this->pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function pdo() : PDO {
        return $this->pdo();
    }

    public function transaction(callable $callback) : mixed {
        $this->pdo->beginTransaction();
        try {
            $hasil = $callback();
            $this->pdo->commit();
            return $hasil;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}