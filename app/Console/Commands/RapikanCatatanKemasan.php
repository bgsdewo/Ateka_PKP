<?php

namespace App\Console\Commands;

use App\Models\StockTransaction;
use Illuminate\Console\Command;

/**
 * Mengubah catatan pembelian per kemasan lama ke format baru.
 *
 * Lama : "Pembelian Rautan Kaca (1 Dus × isi 12 = 12 Buah)"
 * Baru : "Pembelian Rautan Kaca 1 Dus berisi 12 buah"
 *        (lebih dari 1 kemasan: "… 2 Dus masing-masing berisi 12 buah")
 *
 * Mode bawaan hanya MENAMPILKAN perubahan (simulasi). Tambahkan --simpan untuk menulis.
 * Hanya kolom catatan yang diubah; stok dan harga tidak disentuh.
 */
class RapikanCatatanKemasan extends Command
{
    protected $signature = 'atk:rapikan-catatan-kemasan {--simpan : Tulis perubahan ke database (tanpa opsi ini hanya simulasi)}';

    protected $description = 'Ubah catatan pembelian per kemasan lama ke format "… 1 Dus berisi 12 buah"';

    public function handle(): int
    {
        $simpan = (bool) $this->option('simpan');
        $jumlah = 0;

        StockTransaction::query()
            ->where('type', 'masuk')
            ->where('note', 'like', '% × isi %')
            ->orderBy('id')
            ->each(function (StockTransaction $tx) use ($simpan, &$jumlah) {
                $baru = self::ubah((string) $tx->note);
                if ($baru === null || $baru === $tx->note) {
                    return;
                }
                $jumlah++;
                $this->line("#{$tx->id}");
                $this->line("  lama: {$tx->note}");
                $this->line("  baru: {$baru}");
                if ($simpan) {
                    $tx->note = $baru;
                    $tx->save();
                }
            });

        if ($jumlah === 0) {
            $this->info('Tidak ada catatan lama yang perlu dirapikan.');
        } elseif ($simpan) {
            $this->info("{$jumlah} catatan diperbarui.");
        } else {
            $this->warn("{$jumlah} catatan akan diubah. Jalankan ulang dengan --simpan untuk menulis.");
        }

        return self::SUCCESS;
    }

    /** Terjemahkan satu catatan; null bila polanya tidak dikenali. */
    public static function ubah(string $note): ?string
    {
        $pola = '/^Pembelian (.+?) \((\d+) (\S+) × isi (\d+) = \d+ (\S+)\)(.*)$/u';
        if (!preg_match($pola, $note, $m)) {
            return null;
        }
        [, $nama, $qty, $kemasan, $isi, $satuan, $sisa] = $m;

        return "Pembelian {$nama} {$qty} {$kemasan}"
            . ((int) $qty > 1 ? ' masing-masing' : '')
            . " berisi {$isi} " . mb_strtolower($satuan) . $sisa;
    }
}
