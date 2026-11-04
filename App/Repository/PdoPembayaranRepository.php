<?php

namespace App\Repository;

use App\Domains\Pembayaran;
use App\Enums\MetodePembayaran;
use DateTimeImmutable;
use Override;

class PdoPembayaranRepository extends AbstractPdoRepository implements PembayaranRepositoryInterface {

    private function toEntity(array $row) : Pembayaran {
        return new Pembayaran(
            kodeNota: $row['kode_nota'],
            metode: MetodePembayaran::from($row['metode']),
            uangMasuk: (int) $row['uang_masuk'],
            jumlahDibayar: (int) $row['jumlah_dibayar'],
            createAt: new DateTimeImmutable($row['created_at']),
            idPembayaran: (int) $row['id_pembayaran']
        );
    }

    #[Override]
    public function findByKodeNota(string $kodeNota): array
    {
        $rows = $this->fetchAll(
            'SELECT * FROM pembayaran WHERE kode_nota = :kode ORDER BY created_at',
            ['kode' => $kodeNota]
        );
        return array_map( fn(array $row) => $this->toEntity($row), $rows);
    }

    #[Override]
    public function totalDibayar(string $kodeNota): int
    {
        $row = $this->fetchOne(
            'SELECT COALESCE(SUM(jumlah_dibayar), 0) AS total FROM pembayaran WHERE kode_nota = :kode',
            ['kode' => $kodeNota]
        );
        return (int) $row['total'];
    }

    #[Override]
    public function rekapPerTanggal(DateTimeImmutable $tanggal): array
    {
        $row = $this->fetchOne(
            'SELECT COALESCE(SUM(jumlah_dibayar), 0) AS total, COUNT(*) AS jumlah_transaksi FROM pembayaran WHERE DATE(created_at) = :tanggal',
            ['tanggal' => $tanggal->format('Y-m-d')]
        );
        return [
            'total' => (int) $row['total'],
            'jumlah_transaksi' => (int) $row['jumlah_transaksi'],
        ];
    }

    #[Override]
    public function save(Pembayaran $pembayaran): Pembayaran
    {
        $this->execute(
            'INSERT INTO pembayaran (kode_nota, metode, uang_masuk, jumlah_dibayar, created_at) VALUES (:kode_nota, :metode, :uang_masuk, :jumlah_dibayar, :created_at)',
            [
                'kode_nota' => $pembayaran->getKodeNota(),
                'metode' => $pembayaran->getMetode()->value,
                'uang_masuk' => $pembayaran->getUangMasuk(),
                'jumlah_dibayar' => $pembayaran->getJumlahDibayar(),
                'created_at' => $pembayaran->getCreateAt()->format('Y-m-d H:i:s'),
            ]
        );

        return $pembayaran->withId($this->lastInsertId());
    }
}
