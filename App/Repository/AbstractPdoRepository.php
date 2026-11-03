<?php

namespace App\Repository;

use App\Infrastructure\Database;
use App\Service\TransactionManager;

abstract class AbstractPdsoRepository implements TransactionManager {
    public function __construct(
        protected Database $db
    )
    {}

    /**
     * Untuk get data by id atau get data 1 row, mengembalikan null jika
     * tidak ada
     *
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    protected function fetchOne(string $sql, array $params = []) : ?array {
        $stmt = $this->db->pdo()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return $row === false ? null : $row;

    }

    /**
     * untuk bulk fetch semisal get semua data, mengembalikan array kosong
     * jika tidak ada
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    protected function fetchAll(string $sql, array $params = []) : array {
        $stmt = $this->db->pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * untuk Insert, Update, Delete
     *
     * @param string $sql
     * @param array $params
     * @return integer
     */
    protected function excecute(string $sql, array $params = []) : int {
        $stmt = $this->db->pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * mengembalikan id dari insert terakhir
     *
     * @return integer
     */
    protected function lastInsertId() : int {
        return (int) $this->db->pdo()->lastInsertId();
    }

}