<?php
namespace App\Enums;

enum StatusPesanan : string {
    case DITERIMA = "diterima";
    case DICUCI = "dicuci";
    case SELESAI = "selesai";
    case DIAMBIL = "diambil";
    case DIBATALKAN = "dibatalkan";

    public function bisaPindahKe(StatusPesanan $statusBaru) : bool {
        $allowedTransition = match($this) {
            self::DITERIMA => [self::DICUCI, self::DIBATALKAN],
            self::DICUCI => [self::SELESAI],
            self::SELESAI => [self::DIAMBIL],
            self::DIAMBIL, self::DIBATALKAN => []
        };

        return in_array($statusBaru, $allowedTransition, true);
    }
}
