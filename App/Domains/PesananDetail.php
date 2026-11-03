<?php

namespace App\Domains;

use App\Enums\SatuanLayanan;
use App\Domains\Layanan;
use App\Exception\BusinessRuleException;
use App\Exception\ValidationException;

class PesananDetail {
    private ?int $idPesananLayanan;
    private int $idLayanan;
    private string $jenisLayanan;
    private SatuanLayanan $satuan;
    private float $jumlahAktual;
    private int $hargaSatuan;
    private int $totalHarga;

    public function __construct(Layanan $layanan, float $jumlahAktual)
    {
        if (!$layanan->isAktif()) {
            # code...
            throw new BusinessRuleException("Layanan tidak aktif!");
        }
        $this->idLayanan = $layanan->getIdLayanan();
        $this->jenisLayanan = $layanan->getJenisLayanan();
        $this->satuan = $layanan->getSatuan();
        $this->setJumlahAktual($jumlahAktual);
        $this->hargaSatuan = $layanan->getHargaSatuan();
        $this->totalHarga = self::hitungSubTotal();
    }


    function hitungSubTotal() : int {
        $totalHarga = ceil($this->jumlahAktual * $this->hargaSatuan);
        return $totalHarga;
    }


    /**
     * Set the value of jumlahAktual
     *
     * @return  self
     */ 
    public function setJumlahAktual($jumlahAktual)
    {
        if ($jumlahAktual <= 0) {
            # code...
            throw new ValidationException('Jumlah harus lebih dari 0');
        }
        $this->jumlahAktual = $jumlahAktual;

        return $this;
    }
}

?>