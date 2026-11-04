<?php

namespace App\Repository;

use App\Domains\Pesanan;
use App\Service\DTO\PesananFilter;
use DateTimeImmutable;

interface PesananRepositoryInterface {
    public function findByKode(string $kodeNota) : ?Pesanan;

    public function findAll(PesananFilter $filter) : array;

    public function findTerlambat(DateTimeImmutable $sekarang) : array;

    public function kodeExists(string $kodeNota) : bool;

    public function countByPelanggan(int $idPelanggan) : int;

    public function save(Pesanan $pesanan) : void;

    public function updateStatus(Pesanan $pesanan) : void;
}
