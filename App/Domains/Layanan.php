<?php



namespace App\Domains;



use App\Enums\SatuanLayanan;
use App\Enums\StatusLayanan;
use App\Exceptions\ValidationException;

class Layanan {
    private ?int $idLayanan;
    private string $jenisLayanan;
    private SatuanLayanan $satuan;
    private int $hargaSatuan;
    private StatusLayanan $status;

    public function __construct(string $jenisLayanan, SatuanLayanan $satuan, int $hargaSatuan, StatusLayanan $status, ?int $idLayanan = null) {
        $this->idLayanan = $idLayanan;
        $this->setJenisLayanan($jenisLayanan);
        $this->satuan = $satuan;
        $this->setHargaSatuan($hargaSatuan);
        $this->status = $status;
    }

    public function withId(int $id): self {
        $salinan = clone $this;
        $salinan->idLayanan = $id;
        return $salinan;
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
     * Set the value of jenisLayanan
     *
     * @return  self
     */ 
    public function setJenisLayanan($jenisLayanan)
    {
        $jenis = trim($jenisLayanan);
        if ($jenis === '') {
            # code...
            throw new ValidationException("Jenis layanan wajib di isi");
            
        }
        $this->jenisLayanan = $jenisLayanan;

        return $this;
    }

    /**
     * Get the value of satuan
     */ 
    public function getSatuan()
    {
        return $this->satuan;
    }

    /**
     * Set the value of satuan
     *
     * @return  self
     */ 
    public function setSatuan($satuan)
    {
        $this->satuan = $satuan;

        return $this;
    }

    /**
     * Get the value of hargaSatuan
     */ 
    public function getHargaSatuan()
    {
        return $this->hargaSatuan;
    }

    /**
     * Set the value of hargaSatuan
     *
     * @return  self
     */ 
    public function setHargaSatuan($hargaSatuan)
    {
        if ($hargaSatuan <= 0) {
            throw new ValidationException('Harga satuan lebih dari 0');
            # code...
        }

        $this->hargaSatuan = $hargaSatuan;

        return $this;
    }

    public function isAktif() : bool {
        return match($this->status) {
            StatusLayanan::AKTIF => true,
            StatusLayanan::NONAKTIF => false,
            default => false,
        }; 
    }

    // 
    public function aktifkan() : void {
        $this->status = StatusLayanan::AKTIF;
    }

    public function nonaktifkan() : void {
        $this->status = StatusLayanan::NONAKTIF;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }
}

?>