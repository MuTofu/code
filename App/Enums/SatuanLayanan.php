<?php

namespace App\Enums;

enum SatuanLayanan : string {
    case PCS = "pcs";
    case KG = "kg";

    public function minimum() : float {
        return match ($this) {
             self::PCS => 1.0,
             self::KG => 0.0,
        };
    }
}
