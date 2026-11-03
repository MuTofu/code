<?php

namespace App\Repository;

use App\Domains\Pelanggan;

interface PelangganRepositoryInterface {
    public function findById(int $id) : ?Pelanggan;

    public function findByNoTelp(string $noTelp) : ?Pelanggan;

    public function search(?string $keyword) : array;

    public function save(Pelanggan $pelanggan) : Pelanggan;

    public function update(Pelanggan $pelanggan) : void;

    public function delete(int $id) : void;
}