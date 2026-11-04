<?php

namespace App\Domains;


use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Exceptions\BusinessRuleException;
use App\Exceptions\ValidationException;
use DateTimeImmutable;

class Pesanan {
    public function __construct(
        private string $kodeNota,
        private int $idPelanggan,
        private TipePesanan $tipe,
        private StatusPesanan $status,
        private int $totalTagihan,
        private DateTimeImmutable $tanggalMasuk,
        private DateTimeImmutable $estimasi,
        private ?DateTimeImmutable $tanggalDiambil,
        private array $items = [],
    ) { }

    public static function buat(string $kodeNota, int $idPelanggan, TipePesanan $tipe, array $items, DateTimeImmutable $sekarang) : self {
        if ($items === []) {
            throw new ValidationException("Minimal ada 1 layanan!");
        }

        $subtotal = array_sum(array_map(fn ($i) => $i->hitungSubtotal(), $items));
        
        $object = new self(
            kodeNota: $kodeNota,
            idPelanggan: $idPelanggan,
            tipe: $tipe,
            status: StatusPesanan::DITERIMA,
            totalTagihan: $tipe->terapkanBiaya($subtotal),
            tanggalMasuk: $sekarang,
            estimasi: $sekarang->modify('+' . $tipe->durasiHari() . ' days'),
            tanggalDiambil: null,
            items: $items,
        );
        return $object;
    }

    public function sisaTagihan(int $totalBayar) : int {
        return max($this->totalTagihan - $totalBayar, 0);
    }
    
    public function isLunas(int $totalBayar) : bool {
        return $this->sisaTagihan($totalBayar) === 0;
    }

    public function ubahStatus(StatusPesanan $baru, int $totalBayar, DateTimeImmutable $sekarang) {
        if(!$this->status->bisaPindahKe($baru)) {
            throw new ValidationException("Status harus urut!");
        }

        if($baru === StatusPesanan::DIAMBIL && $this->isLunas($totalBayar)) {
            throw new BusinessRuleException("Pesanan belum lunas!");
        }

        $this->status = $baru;
        if($baru === StatusPesanan::DIAMBIL) {
            $this->tanggalDiambil = $sekarang;
        }

    }

    

        /**
         * Get the value of kodeNota
         */ 
        public function getKodeNota()
        {
                return $this->kodeNota;
        }

        /**
         * Get the value of idPelanggan
         */ 
        public function getIdPelanggan()
        {
                return $this->idPelanggan;
        }

        /**
         * Get the value of tipe
         */ 
        public function getTipe()
        {
                return $this->tipe;
        }

        /**
         * Get the value of status
         */ 
        public function getStatus()
        {
                return $this->status;
        }

        /**
         * Get the value of totalTagihan
         */ 
        public function getTotalTagihan()
        {
                return $this->totalTagihan;
        }

        /**
         * Get the value of tanggalMasuk
         */ 
        public function getTanggalMasuk()
        {
                return $this->tanggalMasuk;
        }

        /**
         * Get the value of estimasi
         */ 
        public function getEstimasi()
        {
                return $this->estimasi;
        }

        /**
         * Get the value of tanggalDiambil
         */ 
        public function getTanggalDiambil()
        {
                return $this->tanggalDiambil;
        }

        /**
         * Get the value of items
         */ 
        public function getItems()
        {
                return $this->items;
        }
}