<?php

namespace App\Repository;

use App\Domains\Layanan;
use App\Enums\SatuanLayanan;
use App\Enums\StatusLayanan;
use Override;

class PdoLayananRepository extends AbstractPdoRepository implements LayananRepositoryInterface {

    private function toEntity(array $row) : Layanan {
        return new Layanan(
            jenisLayanan: $row['jenis_layanan'],
            satuan: SatuanLayanan::from($row['satuan']),
            hargaSatuan: (int) $row['harga_satuan'],
            status: StatusLayanan::from($row['status']),
            idLayanan: (int) $row['id_layanan']
        );
    }

    #[Override]
    public function findById(int $id): ?Layanan
    {
        $row = $this->fetchOne('SELECT * FROM layanan WHERE id_layanan = :id', ['id' => $id]);
        return $row === null ? null : $this->toEntity($row);
    }

    #[Override]
    public function findAll(?StatusLayanan $status = null): array
    {
        if ($status === null) {
            $rows = $this->fetchAll('SELECT * FROM layanan ORDER BY jenis_layanan');
        } else {
            $rows = $this->fetchAll(
                'SELECT * FROM layanan WHERE status = :status ORDER BY jenis_layanan',
                ['status' => $status->value]
            );
        }
        return array_map( fn(array $row) => $this->toEntity($row), $rows);
    }

    #[Override]
    public function save(Layanan $layanan): Layanan
    {
        $this->execute(
            'INSERT INTO layanan (jenis_layanan, satuan, harga_satuan, status) VALUES (:jenis_layanan, :satuan, :harga_satuan, :status)',
            [
                'jenis_layanan' => $layanan->getJenisLayanan(),
                'satuan' => $layanan->getSatuan()->value,
                'harga_satuan' => $layanan->getHargaSatuan(),
                'status' => $layanan->getStatus()->value,
            ]
        );

        return $layanan->withId($this->lastInsertId());
    }

    #[Override]
    public function update(Layanan $layanan): void
    {
        $this->execute(
            'UPDATE layanan SET jenis_layanan = :jenis_layanan, satuan = :satuan, harga_satuan = :harga_satuan, status = :status WHERE id_layanan = :id',
            [
                'jenis_layanan' => $layanan->getJenisLayanan(),
                'satuan' => $layanan->getSatuan()->value,
                'harga_satuan' => $layanan->getHargaSatuan(),
                'status' => $layanan->getStatus()->value,
                'id' => $layanan->getIdLayanan(),
            ]
        );
    }
}
