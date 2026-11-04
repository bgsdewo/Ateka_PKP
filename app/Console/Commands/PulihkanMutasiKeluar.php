<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\RequestDetail;
use App\Models\RequestItem;
use App\Models\StockTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Memulihkan mutasi BARANG KELUAR yang hilang untuk permohonan yang sudah berstatus "distributed".
 *
 * Penyebab umum: akun petugas yang mencatat mutasi dihapus dahulu (relasi hapus berantai), sehingga
 * tiket permohonan masih ada di riwayat tetapi baris mutasinya hilang dari kartu persediaan.
 *
 * Mode bawaan hanya MENAMPILKAN apa yang akan dipulihkan (dry-run). Tambahkan --simpan untuk menulis.
 * Stok master barang TIDAK diubah (stok sudah terpotong saat penyerahan); hanya riwayat mutasi yang dibuat.
 */
class PulihkanMutasiKeluar extends Command
{
    protected $signature = 'atk:pulihkan-mutasi-keluar {--simpan : Tulis perubahan ke database (tanpa opsi ini hanya simulasi)}';

    protected $description = 'Pulihkan mutasi barang keluar yang hilang untuk permohonan yang sudah diserahkan';

    public function handle(): int
    {
        $simpan = (bool) $this->option('simpan');

        $tickets = RequestItem::with(['details.product', 'user'])
            ->where('status', 'distributed')
            ->orderBy('distributed_at')
            ->get();

        $missing = [];
        $unlinked = [];

        foreach ($tickets as $ticket) {
            foreach ($ticket->details as $detail) {
                $qty = $detail->effective_quantity;
                if ($qty <= 0) {
                    continue;
                }
                if (!$detail->product_id || !$detail->product) {
                    $unlinked[] = [$ticket->request_code, $detail->item_name, $qty];
                    continue;
                }
                $exists = StockTransaction::where('type', 'keluar')
                    ->where('product_id', $detail->product_id)
                    ->where('no_faktur', $ticket->request_code)
                    ->exists();
                if (!$exists) {
                    $missing[] = [$ticket, $detail, $qty];
                }
            }
        }

        if (empty($missing)) {
            $this->info('Semua permohonan yang sudah diserahkan memiliki mutasi barang keluar. Tidak ada yang perlu dipulihkan.');
        } else {
            $this->warn(count($missing) . ' mutasi barang keluar hilang:');
            $this->table(
                ['Kode Tiket', 'Barang', 'Jumlah', 'Tanggal Serah', 'Penerima'],
                array_map(fn($m) => [
                    $m[0]->request_code,
                    $m[1]->item_name,
                    $m[2],
                    optional($m[0]->distributed_at)->format('d-m-Y') ?: '-',
                    $m[0]->receiver_name ?: ($m[0]->user->name ?? '-'),
                ], $missing)
            );
        }

        if (!empty($unlinked)) {
            $this->line('');
            $this->warn('Barang berikut tidak terhubung ke Master Barang sehingga tidak bisa dipulihkan otomatis:');
            $this->table(['Kode Tiket', 'Barang', 'Jumlah'], $unlinked);
        }

        if (empty($missing)) {
            return self::SUCCESS;
        }

        if (!$simpan) {
            $this->line('');
            $this->comment('Ini hanya simulasi. Jalankan dengan opsi --simpan untuk memulihkan data.');
            return self::SUCCESS;
        }

        $userIdNullable = collect(Schema::getColumns('stock_transactions'))
            ->firstWhere('name', 'user_id')['nullable'] ?? false;
        $needsNullUser = collect($missing)->contains(fn($m) => empty($m[0]->distributed_by));
        if ($needsNullUser && !$userIdNullable) {
            $this->error('Jalankan "php artisan migrate" terlebih dahulu, lalu ulangi perintah ini.');
            return self::FAILURE;
        }

        DB::transaction(function () use ($missing) {
            $productIds = [];
            foreach ($missing as [$ticket, $detail, $qty]) {
                $product = Product::find($detail->product_id);
                $penerima = $ticket->receiver_name ?: ($ticket->user->name ?? 'Pemohon');
                $pemohon = $ticket->user ? ($ticket->user->name_with_title ?: $ticket->user->name) : 'Pemohon';

                StockTransaction::create([
                    'product_id'       => $detail->product_id,
                    'user_id'          => $ticket->distributed_by,
                    'type'             => 'keluar',
                    'recipient_type'   => $ticket->type ?: 'individu',
                    'quantity'         => $qty,
                    'stock_after'      => 0, // dihitung ulang di bawah
                    'transaction_date' => optional($ticket->distributed_at)->toDateString() ?: now()->toDateString(),
                    'no_faktur'        => $ticket->request_code,
                    'uraian'           => $penerima . ($penerima !== $pemohon ? ' (untuk ' . $pemohon . ')' : ''),
                    'kondisi'          => 'Baik',
                    'harga_perolehan'  => $product->purchase_price ?? 0,
                    'note'             => 'Penyerahan ATK kepada ' . $penerima . ' (' . $ticket->request_code . ') [dipulihkan dari riwayat permohonan]',
                ]);
                $productIds[$detail->product_id] = true;
            }

            // Hitung ulang saldo berjalan (stock_after) per barang. Stok master tidak diubah.
            foreach (array_keys($productIds) as $pid) {
                $running = 0;
                $rows = StockTransaction::where('product_id', $pid)
                    ->orderBy('transaction_date')
                    ->orderBy('id')
                    ->get();
                foreach ($rows as $row) {
                    $running += $row->type === 'masuk' ? (int) $row->quantity : -(int) $row->quantity;
                    $row->stock_after = max(0, $running);
                    $row->save();
                }
            }
        });

        $this->info(count($missing) . ' mutasi barang keluar berhasil dipulihkan.');
        return self::SUCCESS;
    }
}
