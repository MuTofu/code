<?php

namespace App\Repository;

use App\Domains\Layanan;
use App\Enums\StatusLayanan;

interface LayananRepositoryInterface {
    public function findById(int $id) : ?Layanan;

    public function findAll(?StatusLayanan $status = null) : array;

    public function save(Layanan $layanan) : Layanan;

    public function update(Layanan $layanan) : void;
}
