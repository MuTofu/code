<?php

namespace App\Repository;

use App\Domains\Pelanggan;
use App\Exceptions\BusinessRuleException;
use Override;
use PDOException;

class PdoPelangganRepository extends AbstractPdoRepository implements PelangganRepositoryInterface {

    private  function toEntity(array $row) : Pelanggan {
        return new Pelanggan(
            nama: $row['nama'],
            noTelp: $row['no_telp'],
            alamat: $row['alamat'],
            idPelanggan: (int) $row['id_pelanggan']
        );
    }

    private function duplicateCheck(callable $query) : void {
        try {
            $query();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new BusinessRuleException("Nomor HP sudah ada !");
            }
            throw $e;
        }
    }

    #[Override]
    public function findById(int $id): ?Pelanggan
    {
        $row = $this->fetchOne('SELECT * FROM pelanggan WHERE id_pelanggan = :id', ['id' => $id]);
        return $row === null ? null : $this->toEntity($row);
    }

    #[Override]
    public function findByNoTelp(string $noTelp): ?Pelanggan
    {
        $row = $this->fetchOne('SELECT * FROM pelanggan WHERE no_telp = :noTelp', ['noTelp' => $noTelp]);
        return $row === null ? null : $this->toEntity($row);
    }

    #[Override]
    public function search(?string $keyword): array
    {
        if ($keyword === null || trim($keyword) === '') {
            $rows = $this->fetchAll('SELECT * FROM pelanggan');
        } else {
            $rows = $this->fetchAll(
                'SELECT * FROM pelanggan WHERE LOWER(nama) LIKE :keyword',
                ['keyword' => '%' . strtolower(trim($keyword)) . '%']
            );
        }
        return array_map( fn(array $row) => $this->toEntity($row), $rows);
    }

    #[Override]
    public function save(Pelanggan $pelanggan): Pelanggan
    {
        $this->duplicateCheck( fn () => $this->execute(
            'INSERT INTO pelanggan (nama, no_telp, alamat) VALUES (:nama, :no_telp, :alamat)',
            [
                'nama' => $pelanggan->getNama(),
                'no_telp' => $pelanggan->getNoTelp(),
                'alamat' => $pelanggan->getAlamat()
            ]));

        return $pelanggan->withId($this->lastInsertId());
    }

    #[Override]
    public function update(Pelanggan $pelanggan): void
    {
        $this->duplicateCheck( fn () => $this->execute(
            'UPDATE pelanggan SET nama = :nama, no_telp = :no_telp, alamat = :alamat WHERE id_pelanggan = :id',
            [
                'nama' => $pelanggan->getNama(),
                'no_telp' => $pelanggan->getNoTelp(),
                'alamat' => $pelanggan->getAlamat(),
                'id' => $pelanggan->getIdPelanggan(),
            ]
        ));
    }

    #[Override]
    public function delete(int $id): void
    {
        $this->execute('DELETE FROM pelanggan WHERE id_pelanggan = :id', ['id' => $id]);
    }




}