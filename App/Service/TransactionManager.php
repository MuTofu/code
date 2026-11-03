<?php

namespace App\Service;


interface TransactionManager {
    public function transaction(callable $callback) : mixed;
}