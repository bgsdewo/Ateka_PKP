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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'kode_barang')) {
                $table->string('kode_barang', 50)->nullable()->after('name');
            }
        });

        // Mapping kode barang BMN resmi dari Excel Lembar ke-3
        $bmnMapping = [
            'Pensil' => '1010301001',
            'Pulpen' => '1010301001',
            'Spidol' => '1010301001',
            'Stabilo' => '1010301001',
            'Tinta Pulpen' => '1010301002',
            'Binder Clip' => '1010301003',
            'Paper Clip' => '1010301003',
            'Penghapus' => '1010301004',
            'Tipe Ex' => '1010301004',
            'Buku' => '1010301005',
            'Ordner' => '1010301006',
            'Clear Holder' => '1010301006',
            'Box File' => '1010301006',
            'Sheet Protector' => '1010301006',
            'Map' => '1010301006',
            'Business File' => '1010301006',
            'Penggaris' => '1010301007',
            'Cutter' => '1010301008',
            'Lem' => '1010301010',
            'Double Tape' => '1010301010',
            'Selotip' => '1010301010',
            'Lakban' => '1010301010',
            'Label Sticker' => '1010301010',
            'Post It' => '1010301010',
            'Stapler' => '1010301012',
            'Isi Staples' => '1010301013',
            'Elevated Tray' => '1010301999',
            'Gunting' => '1010301999',
            'Kalkulator' => '1010301999',
            'Pembolong Kertas' => '1010301999',
            'Rautan Pensil' => '1010301999',
            'Tape Cutter' => '1010301999',
            'Tape Dispenser / Dispenser Tape' => '1010301999',
            'Tape Dispenser' => '1010301999',
            'Laser Pointer' => '1010301999',
            'Laser pointer' => '1010301999',
            'Bak Stempel' => '1010301999',
            'Book Stand' => '1010301999',
            'V-Tech Desk Organizer' => '1010301999',
            'V-Tech Round Pencil Cup' => '1010301999',
            'Recorder' => '1010301999',
            'Ring O-Bin' => '1010301999',
            'Kertas' => '1010302001',
            'Amplop' => '1010302004',
            'Transparant Sheet' => '1010303001',
            'Pita' => '1010304003',
            'Pita Printer' => '1010304003',
            'Tinta Brother' => '1010304004',
            'Tinta Canon' => '1010304004',
            'Tinta Epson' => '1010304004',
            'Tinta HP' => '1010304004',
            'Toner Printer' => '1010304004',
            'Converter / USB HUB' => '1010304006',
            'Flashdisk' => '1010304006',
            'USB' => '1010304006',
            'Mouse Wireless' => '1010304010',
            'Mouse' => '1010304010',
            'Hardisk Eksternal' => '1010304999',
            'Keyboard' => '1010304999',
            'Baterai' => '1010306010',
        ];

        foreach ($bmnMapping as $catName => $kodeBarang) {
            DB::table('categories')
                ->where('name', $catName)
                ->update(['kode_barang' => $kodeBarang]);
        }

        // Untuk kategori lainnya yang memiliki format di deskripsi, ekstrak kode barangnya
        $categories = DB::table('categories')->get();
        foreach ($categories as $cat) {
            if (empty($cat->kode_barang) && !empty($cat->description)) {
                if (preg_match('/(?:Kode Barang BMN:\s*)(\d{10})/i', $cat->description, $matches)) {
                    DB::table('categories')->where('id', $cat->id)->update(['kode_barang' => $matches[1]]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'kode_barang')) {
                $table->dropColumn('kode_barang');
            }
        });
    }
};
