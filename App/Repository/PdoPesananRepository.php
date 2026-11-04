<?php

namespace App\Repository;

use App\Domains\Pesanan;
use App\Domains\PesananDetail;
use App\Enums\SatuanLayanan;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Service\DTO\PesananFilter;
use DateTimeImmutable;
use Override;

class PdoPesananRepository extends AbstractPdoRepository implements PesananRepositoryInterface {

    private const FORMAT_WAKTU = 'Y-m-d H:i:s';

    private function toEntity(array $row, array $items) : Pesanan {
        return new Pesanan(
            kodeNota: $row['kode_nota'],
            idPelanggan: (int) $row['id_pelanggan'],
            tipe: TipePesanan::from($row['tipe']),
            status: StatusPesanan::from($row['status']),
            totalTagihan: (int) $row['total_tagihan'],
            tanggalMasuk: new DateTimeImmutable($row['tanggal_masuk']),
            estimasi: new DateTimeImmutable($row['estimasi']),
            tanggalDiambil: $row['tanggal_diambil'] === null ? null : new DateTimeImmutable($row['tanggal_diambil']),
            items: $items
        );
    }

    private function toItem(array $row) : PesananDetail {
        return new PesananDetail(
            idLayanan: (int) $row['id_layanan'],
            jenisLayanan: $row['jenis_layanan'],
            satuan: SatuanLayanan::from($row['satuan']),
            jumlahAktual: (float) $row['jumlah_aktual'],
            hargaSatuan: (int) $row['harga_satuan'],
            idPesananLayanan: (int) $row['id_pesanan_layanan']
        );
    }

    /**
     * Ambil item untuk banyak nota sekaligus dalam 1 query (hindari N+1).
     *
     * @param string[] $kodeNotas
     * @return array<string, PesananDetail[]> dikelompokkan per kode_nota
     */
    private function findItems(array $kodeNotas) : array {
        if ($kodeNotas === []) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach (array_values($kodeNotas) as $i => $kode) {
            $placeholders[] = ':kode' . $i;
            $params['kode' . $i] = $kode;
        }

        $rows = $this->fetchAll(
            'SELECT * FROM pesanan_layanan WHERE kode_nota IN (' . implode(', ', $placeholders) . ')',
            $params
        );

        // TODO: CEK PAKAI DEBUG UNTUK TAU HASIL NYA
        $items = [];
        foreach ($rows as $row) {
            $items[$row['kode_nota']][] = $this->toItem($row);
        }
        return $items;
    }

    /**
     * Ubah banyak baris pesanan jadi entity, lengkap dengan item-nya.
     *
     * @return Pesanan[]
     */
    private function toEntities(array $rows) : array {
        $items = $this->findItems(array_column($rows, 'kode_nota'));
        return array_map(
            fn(array $row) => $this->toEntity($row, $items[$row['kode_nota']] ?? []),
            $rows
        );
    }

    #[Override]
    public function findByKode(string $kodeNota): ?Pesanan
    {
        $row = $this->fetchOne('SELECT * FROM pesanan WHERE kode_nota = :kode', ['kode' => $kodeNota]);
        return $row === null ? null : $this->toEntities([$row])[0];
    }

    #[Override]
    public function findAll(PesananFilter $filter): array
    {
        $kondisi = [];
        $params = [];

        if ($filter->status !== null) {
            $kondisi[] = 'status = :status';
            $params['status'] = $filter->status->value;
        }
        if ($filter->idPelanggan !== null) {
            $kondisi[] = 'id_pelanggan = :id_pelanggan';
            $params['id_pelanggan'] = $filter->idPelanggan;
        }
        if ($filter->keyword !== null && trim($filter->keyword) !== '') {
            $kondisi[] = 'kode_nota LIKE :keyword';
            $params['keyword'] = '%' . trim($filter->keyword) . '%';
        }

        $where = $kondisi === [] ? '' : ' WHERE ' . implode(' AND ', $kondisi);
        $rows = $this->fetchAll('SELECT * FROM pesanan' . $where . ' ORDER BY tanggal_masuk DESC', $params);

        return $this->toEntities($rows);
    }

    #[Override]
    public function findTerlambat(DateTimeImmutable $sekarang): array
    {
        $rows = $this->fetchAll(
            'SELECT * FROM pesanan WHERE estimasi < :sekarang AND status IN (:diterima, :dicuci) ORDER BY estimasi',
            [
                'sekarang' => $sekarang->format(self::FORMAT_WAKTU),
                'diterima' => StatusPesanan::DITERIMA->value,
                'dicuci' => StatusPesanan::DICUCI->value,
            ]
        );
        return $this->toEntities($rows);
    }

    #[Override]
    public function kodeExists(string $kodeNota): bool
    {
        return $this->fetchOne('SELECT 1 FROM pesanan WHERE kode_nota = :kode LIMIT 1', ['kode' => $kodeNota]) !== null;
    }

    #[Override]
    public function countByPelanggan(int $idPelanggan): int
    {
        $row = $this->fetchOne(
            'SELECT COUNT(*) AS jumlah FROM pesanan WHERE id_pelanggan = :id',
            ['id' => $idPelanggan]
        );
        return (int) $row['jumlah'];
    }

    /**
     * Simpan pesanan beserta item-nya. Tidak membuka transaksi sendiri:
     * service wajib memanggilnya di dalam TransactionManager::transaction().
     */
    #[Override]
    public function save(Pesanan $pesanan): void
    {
        $this->execute(
            'INSERT INTO pesanan (kode_nota, id_pelanggan, tipe, status, total_tagihan, tanggal_masuk, estimasi, tanggal_diambil)
             VALUES (:kode_nota, :id_pelanggan, :tipe, :status, :total_tagihan, :tanggal_masuk, :estimasi, :tanggal_diambil)',
            [
                'kode_nota' => $pesanan->getKodeNota(),
                'id_pelanggan' => $pesanan->getIdPelanggan(),
                'tipe' => $pesanan->getTipe()->value,
                'status' => $pesanan->getStatus()->value,
                'total_tagihan' => $pesanan->getTotalTagihan(),
                'tanggal_masuk' => $pesanan->getTanggalMasuk()->format(self::FORMAT_WAKTU),
                'estimasi' => $pesanan->getEstimasi()->format(self::FORMAT_WAKTU),
                'tanggal_diambil' => $pesanan->getTanggalDiambil()?->format(self::FORMAT_WAKTU),
            ]
        );

        foreach ($pesanan->getItems() as $item) {
            $this->execute(
                'INSERT INTO pesanan_layanan (kode_nota, id_layanan, jenis_layanan, satuan, jumlah_aktual, harga_satuan, total_harga)
                 VALUES (:kode_nota, :id_layanan, :jenis_layanan, :satuan, :jumlah_aktual, :harga_satuan, :total_harga)',
                [
                    'kode_nota' => $pesanan->getKodeNota(),
                    'id_layanan' => $item->getIdLayanan(),
                    'jenis_layanan' => $item->getJenisLayanan(),
                    'satuan' => $item->getSatuan()->value,
                    'jumlah_aktual' => $item->getJumlahAktual(),
                    'harga_satuan' => $item->getHargaSatuan(),
                    'total_harga' => $item->getTotalHarga(),
                ]
            );
        }
    }

    #[Override]
    public function updateStatus(Pesanan $pesanan): void
    {
        $this->execute(
            'UPDATE pesanan SET status = :status, tanggal_diambil = :tanggal_diambil WHERE kode_nota = :kode',
            [
                'status' => $pesanan->getStatus()->value,
                'tanggal_diambil' => $pesanan->getTanggalDiambil()?->format(self::FORMAT_WAKTU),
                'kode' => $pesanan->getKodeNota(),
            ]
        );
    }
}
