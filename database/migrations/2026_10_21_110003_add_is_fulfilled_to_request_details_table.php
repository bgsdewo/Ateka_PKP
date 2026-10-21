<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('request_details', function (Blueprint $table) {
            $table->boolean('is_fulfilled')->default(false)->after('verification_note');
            $table->timestamp('fulfilled_at')->nullable()->after('is_fulfilled');
        });

        // 1. Permintaan stok persediaan biasa (bukan pengadaan) otomatis dianggap fulfilled
        DB::table('request_details')
            ->whereIn('request_id', function ($q) {
                $q->select('id')->from('requests')->where('is_procurement', false);
            })
            ->update(['is_fulfilled' => true]);

        // 2. Untuk pengadaan barang lama yang sudah selesai dibeli sebelum fitur ini ada:
        $procurementRequests = DB::table('requests')
            ->where('is_procurement', true)
            ->whereNotNull('purchase_invoice')
            ->get();

        foreach ($procurementRequests as $pr) {
            $details = DB::table('request_details')->where('request_id', $pr->id)->get();
            $unfulfilledFound = false;

            foreach ($details as $d) {
                // Cek apakah item ini benar-benar sudah memiliki transaksi barang masuk dengan nomor faktur pembelian tersebut
                $hasTx = false;
                if ($d->product_id) {
                    $hasTx = DB::table('stock_transactions')
                        ->where('product_id', $d->product_id)
                        ->where('no_faktur', $pr->purchase_invoice)
                        ->exists();
                }

                if ($hasTx) {
                    DB::table('request_details')->where('id', $d->id)->update([
                        'is_fulfilled' => true,
                        'fulfilled_at' => $pr->purchase_date ?: now(),
                    ]);
                } else {
                    $unfulfilledFound = true;
                    DB::table('request_details')->where('id', $d->id)->update([
                        'is_fulfilled' => false,
                    ]);
                }
            }

            // Jika masih ada item pengadaan di tiket ini yang belum sempat diisi stoknya,
            // kembalikan purchase_invoice ke null agar tiket tetap menunggu penambahan stok item lainnya
            if ($unfulfilledFound) {
                DB::table('requests')->where('id', $pr->id)->update([
                    'purchase_invoice' => null,
                    'purchase_date' => null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_details', function (Blueprint $table) {
            $table->dropColumn(['is_fulfilled', 'fulfilled_at']);
        });
    }
};
