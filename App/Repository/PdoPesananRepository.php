<?php

namespace App\Repository;

use App\Domains\Pelanggan;
use App\Exception\BusinessRuleException;
use Override;
use PDOException;

class PdoPesananRepository extends AbstractPdsoRepository implements PelangganRepositoryInterface {

    private  function toEntity(array $row) : Pelanggan {
        return new Pelanggan(
            nama: $row['nama'],
            noTelp: $row['noTelp'],
            alamat: $row['alamat'],
            idPelanggan: (int) $row['id_pelanggan']
        );
    }

    private function duplicateCheck(callable $query) : void {
        try {
            $query();
        } catch (PDOException $e) {
            if ($e->getCode() === 23000) {
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
        $row = $this->fetchOne('SELECT * FROM pelanggan WHERE noTelp = : noTElp', ['noTelp' => $noTelp]);
        return $row === null ? null : $this->toEntity($row);
    }

    #[Override]
    public function search(?string $keyword): array
    {
        $keyword = strtolower($keyword);
        $row = $this->fetchAll('SELECT * FROM pelanggan WHERE nama = : nama', ['nama' => $keyword]);
        return array_map( fn(array $row) => $this->toEntity($row), $row);
    }

    #[Override]
    public function save(Pelanggan $pelanggan): Pelanggan
    {
        $this->duplicateCheck( fn () => $this->excecute(
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
        
    }


}