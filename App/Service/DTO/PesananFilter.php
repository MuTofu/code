<?php

namespace App\Service\DTO;

use App\Enums\StatusPesanan;

/**
 * Kriteria pencarian pesanan. Semua field opsional, null = tidak difilter.
 */
final readonly class PesananFilter {
    public function __construct(
        public ?StatusPesanan $status = null,
        public ?int $idPelanggan = null,
        public ?string $keyword = null,
    ) {}
}
