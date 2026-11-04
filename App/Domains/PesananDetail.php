<?php

namespace App\Domains;

use App\Enums\SatuanLayanan;
use App\Domains\Layanan;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\ValidationException;

class PesananDetail
{
    private ?int $idPesananLayanan;
    private int $idLayanan;
    private string $jenisLayanan;
    private SatuanLayanan $satuan;
    private float $jumlahAktual;
    private int $hargaSatuan;
    private int $totalHarga;

    public function __construct(
        int $idLayanan,
        string $jenisLayanan,
        SatuanLayanan $satuan,
        float $jumlahAktual,
        int $hargaSatuan,
        ?int $idPesananLayanan = null,
    ) {
        $this->idLayanan = $idLayanan;
        $this->jenisLayanan = $jenisLayanan;
        $this->satuan = $satuan;
        $this->setJumlahAktual($jumlahAktual);
        $this->hargaSatuan = $hargaSatuan;
        $this->totalHarga = $this->hitungSubTotal();
        $this->idPesananLayanan = $idPesananLayanan;
    }

    /**
     * Create a new instance of PesananDetail
     *
     * @param Layanan $layanan
     * @param float $jumlahAktual
     * @return self
     */
    public static function buat(Layanan $layanan, float $jumlahAktual): self
    {
        if (!$layanan->isAktif()) {
            throw new BusinessRuleException("Layanan tidak aktif!");
        }
        return new self(
            idLayanan: $layanan->getIdLayanan(),
            jenisLayanan: $layanan->getJenisLayanan(),
            satuan: $layanan->getSatuan(),
            jumlahAktual: $jumlahAktual,
            hargaSatuan: $layanan->getHargaSatuan()
        );
    }



    function hitungSubTotal(): int
    {
        $totalHarga = ceil($this->jumlahAktual * $this->hargaSatuan);
        return $totalHarga;
    }


    /**
     * Set the value of jumlahAktual
     *
     * @return  self
     */
    private function setJumlahAktual($jumlahAktual)
    {
        if ($jumlahAktual <= 0) {
            # code...
            throw new ValidationException('Jumlah harus lebih dari 0');
        }
        $this->jumlahAktual = $jumlahAktual;

        return $this;
    }

    /**
     * Get the value of idPesananLayanan
     */
    public function getIdPesananLayanan()
    {
        return $this->idPesananLayanan;
    }

    /**
     * Get the value of idLayanan
     */
    public function getIdLayanan()
    {
        return $this->idLayanan;
    }

    /**
     * Get the value of jenisLayanan
     */
    public function getJenisLayanan()
    {
        return $this->jenisLayanan;
    }

    /**
     * Get the value of satuan
     */
    public function getSatuan()
    {
        return $this->satuan;
    }

    /**
     * Get the value of totalHarga
     */
    public function getTotalHarga()
    {
        return $this->totalHarga;
    }

    /**
     * Get the value of jumlahAktual
     */ 
    public function getJumlahAktual()
    {
        return $this->jumlahAktual;
    }

    /**
     * Get the value of hargaSatuan
     */ 
    public function getHargaSatuan()
    {
        return $this->hargaSatuan;
    }
}
