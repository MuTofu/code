<?php
namespace App\Enums;

enum TipePesanan : string {
    case REGULAR = "regular";
    case EXPRESS = "express";

    public function durasiHari() : int {
        return match ($this) {
            self::EXPRESS => 1,
            self::REGULAR => 3,
        };
    }

    public function terapkanBiaya(int $subTotal) : int {
        return match ($this) {
            self::EXPRESS => $subTotal * 1.5,
            self::REGULAR => $subTotal * 1,
        };
    }
}

?>