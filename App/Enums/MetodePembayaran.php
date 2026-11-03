<?php

namespace App\Enums;

enum MetodePembayaran : string {
    case TUNAI = "tunai";
    case TRANSFER = "transfer";
    case QRIS = "qris";
}
