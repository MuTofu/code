<?php

namespace App\Domains;

use App\Enums\MetodePembayaran;
use App\Exceptions\ValidationException;
use DateTimeImmutable;

class Pembayaran {
    private ?int $idPembayaran;
    private string $kodeNota;
    private MetodePembayaran $metode;
    private int $uangMasuk;
    private int $jumlahDibayar;
    private DateTimeImmutable $createAt;

    public function __construct(string $kodeNota, MetodePembayaran $metode, int $uangMasuk, int $jumlahDibayar, DateTimeImmutable $createAt, ?int $idPembayaran = null) {
        $this->setKodeNota($kodeNota);
        $this->setMetode($metode);
        $this->setUangMasuk($uangMasuk);
        $this->setJumlahDibayar($jumlahDibayar);
        $this->idPembayaran = $idPembayaran;
        $this->createAt = $createAt;
    }

    public function withId(int $id): self {
        $salinan = clone $this;
        $salinan->idPembayaran = $id;
        return $salinan;
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
        if ($jumlahDibayar <= 0) {
            # code...
            throw new ValidationException("jumlah dibayar harus lebih dari 0!");
        };
        
        $this->jumlahDibayar = $jumlahDibayar;

        return $this;
    }

    /**
     * Get the value of idPembayaran
     */ 
    public function getIdPembayaran()
    {
        return $this->idPembayaran;
    }

    /**
     * Get the value of kodeNota
     */ 
    public function getKodeNota()
    {
        return $this->kodeNota;
    }

    /**
     * Get the value of metode
     */ 
    public function getMetode()
    {
        return $this->metode;
    }

    /**
     * Get the value of uangMasuk
     */ 
    public function getUangMasuk()
    {
        return $this->uangMasuk;
    }

    /**
     * Get the value of jumlahDibayar
     */ 
    public function getJumlahDibayar()
    {
        return $this->jumlahDibayar;
    }

    /**
     * Get the value of createAt
     */ 
    public function getCreateAt()
    {
        return $this->createAt;
    }
}

?>