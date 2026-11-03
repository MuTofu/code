<?php

namespace App\Domains;

use App\Enums\MetodePembayaran;
use App\Exception\ValidationException;
use DateTimeImmutable;

class Pembayaran {
    private ?int $idPembayaran;
    private string $kodeNota;
    private MetodePembayaran $metode;
    private int $uangMasuk;
    private int $jumlahDibayar;
    private DateTimeImmutable $createAt;

    public function __construct(string $kodeNota, MetodePembayaran $metode, int $uangMasuk, int $jumlahDibayar) {
        $this->setKodeNota($kodeNota);
        $this->setMetode($metode);
        $this->setUangMasuk($uangMasuk);
        $this->setJumlahDibayar($jumlahDibayar);
        
    }

    /**
     * Set the value of kodeNota
     *
     * @return  self
     */ 
    public function setKodeNota($kodeNota)
    {
        $this->kodeNota = $kodeNota;

        return $this;
    }

    /**
     * Set the value of metode
     *
     * @return  self
     */ 
    public function setMetode($metode)
    {
        $this->metode = $metode;

        return $this;
    }

    /**
     * Set the value of uangMasuk
     *
     * @return  self
     */ 
    public function setUangMasuk($uangMasuk)
    {
        if ($uangMasuk <= 0) {
            # code...
            throw new ValidationException("uang massuk harus lebih dari 0!");
        }
        $this->uangMasuk = $uangMasuk;

        return $this;
    }

    

    /**
     * Set the value of jumlahDibayar
     *
     * @return  self
     */ 
    public function setJumlahDibayar($jumlahDibayar)
    {
        $this->jumlahDibayar = $jumlahDibayar;

        return $this;
    }
}

?>