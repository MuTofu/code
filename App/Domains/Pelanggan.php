<?php

namespace App\Domains;

use App\Exception\ValidationException;

class Pelanggan {
    private ?int $idPelanggan;
    private string $nama;
    private string $noTelp;
    private ?string $alamat;


    public function __construct(string $nama, string $noTelp, string $alamat, ?int $idPelanggan = null,) {
        $this->idPelanggan = $idPelanggan;
        $this->nama = $nama;
        $this->noTelp = $noTelp;
        $this->alamat = $alamat;
    }

    public function withId(int $id): self {
        $salinan = clone $this;
        $salinan->idPelanggan = $id;
        return $salinan;
    }
    

    /**
     * Get the value of idPelanggan
     */ 
    public function getIdPelanggan()
    {
        return $this->idPelanggan;
    }


    /**
     * Get the value of nama
     */ 
    public function getNama()
    {
        return $this->nama;
    }

    /**
     * Set the value of nama
     *
     * @return  self
     */ 
    public function setNama($nama)
    {
        $namaUser = trim($nama);
        if ($namaUser === '') {
            # code...
            throw new ValidationException("Nama wajib diisi");
        }

        if (mb_strlen($namaUser) >= 100) {
            # code...
            throw new ValidationException("Nama maksimal 100 huruf");
        }
        $this->nama = $nama;

        return $this;
    }

    /**
     * Get the value of noTelp
     */ 
    public function getNoTelp()
    {
        return $this->noTelp;
    }

    /**
     * Set the value of noTelp
     *
     * @return  self
     */ 

    /// TODO : Pelajari regex untuk no telp
    public function setNoTelp($noTelp)
    {
        $this->noTelp = $noTelp;

        return $this;
    }

    /**
     * Get the value of alamat
     */ 
    public function getAlamat()
    {
        return $this->alamat;
    }

    /**
     * Set the value of alamat
     *
     * @return  self
     */ 
    public function setAlamat($alamat)
    {
        $alamat = trim($alamat);
        $this->alamat = $alamat === '' ? null : $alamat;

        return $this;
    }


}

?>