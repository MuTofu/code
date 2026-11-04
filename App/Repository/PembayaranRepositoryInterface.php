<?php

namespace App\Repository;

use App\Domains\Pembayaran;
use DateTimeImmutable;

interface PembayaranRepositoryInterface {
    public function findByKodeNota(string $kodeNota) : array;

    public function totalDibayar(string $kodeNota) : int;

    /**
     * @return array{total: int, jumlah_transaksi: int}
     */
    public function rekapPerTanggal(DateTimeImmutable $tanggal) : array;

    public function save(Pembayaran $pembayaran) : Pembayaran;
}
