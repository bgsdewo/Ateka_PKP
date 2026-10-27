<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $jsonPath = database_path('seeders/data/lembar2_atk_catalog.json');
        if (!file_exists($jsonPath)) {
            $jsonPath = base_path('database/seeders/data/lembar2_atk_catalog.json');
        }

        if (!file_exists($jsonPath)) {
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data || empty($data['categories']) || empty($data['items'])) {
            return;
        }

        DB::transaction(function () use ($data) {
            // 1. Sinkronkan seluruh 56 Kategori dari Sheet 3 / Lembar 2 LIST ATK
            $categoryMap = [];
            $categoryInfoMap = [];
            $validCategoryNames = [];

            foreach ($data['categories'] as $catData) {
                $cName = trim($catData['name']);
                $kdBrg = trim($catData['kd_brg'] ?? '');
                $kdRincian = trim($catData['kd_rincian'] ?? '');
                $bmnGroup = trim($catData['bmn_group'] ?? '');

                $desc = '';
                if ($kdBrg !== '' && !str_contains($kdBrg, '…')) {
                    $desc = "Kode Barang BMN: {$kdBrg}" . ($bmnGroup !== '' ? " ({$bmnGroup})" : '');
                } else {
                    $desc = "Kategori Standar ATK ({$cName})";
                }

                $category = Category::updateOrCreate(
                    ['name' => $cName],
                    [
                        'kode_barang' => $kdBrg ?: null,
                        'kd_rincian' => $kdRincian ?: null,
                        'description' => $desc,
                    ]
                );

                $categoryMap[$cName] = $category->id;
                $categoryInfoMap[$cName] = [
                    'id' => $category->id,
                    'kd_brg' => $kdBrg,
                    'kd_rincian' => $kdRincian,
                    'bmn_group' => $bmnGroup,
                ];
                $validCategoryNames[] = $cName;
            }

            // 2. Remap kategori lama ke kategori standar Lembar 2 jika ada produk di kategori lama
            $legacyRemap = [
                'Kertas HVS' => 'Kertas',
                'Isi Stapler' => 'Isi Staples',
                'Punch/Pembolong' => 'Pembolong Kertas',
                'POST' => 'Post It',
                'Lakband' => 'Lakban',
                'Celotif' => 'Selotip',
                'Batterey Alkaline' => 'Baterai',
                'Binder' => 'Binder Clip',
                'Box' => 'Box File',
                'Mouse' => 'Mouse Wireless',
                'Toner' => 'Toner Printer',
                'Balliner' => 'Pulpen',
                'Ballpoint' => 'Pulpen',
                'Tinta' => 'Tinta Epson',
            ];

            foreach ($legacyRemap as $oldName => $newName) {
                if (isset($categoryMap[$newName])) {
                    $oldCat = Category::where('name', $oldName)->first();
                    if ($oldCat && $oldCat->id !== $categoryMap[$newName]) {
                        Product::where('category_id', $oldCat->id)->update(['category_id' => $categoryMap[$newName]]);
                    }
                }
            }

            // Hapus kategori lama yang kosong dan tidak ada di Lembar 2
            Category::whereNotIn('name', $validCategoryNames)
                ->whereDoesntHave('products')
                ->delete();

            // 3. Sinkronkan seluruh 572 Barang ATK dari Lembar 2
            $seededTimestamp = '2026-01-01 08:00:00';

            foreach ($data['items'] as $idx => $item) {
                $catName = trim($item['category']);
                $catId = $categoryMap[$catName] ?? Category::firstOrCreate(['name' => $catName])->id;

                $name = trim($item['name']);
                $primaryUnit = trim($item['primary_unit'] ?: 'Buah');
                $units = array_values(array_unique(array_filter(array_map('trim', $item['units'] ?? [$primaryUnit]))));
                $availableUnitsStr = implode(',', $units);

                $catInfo = $categoryInfoMap[$catName] ?? null;
                $kdBrg = trim($item['kd_brg'] ?? ($catInfo['kd_brg'] ?? ''));
                if (str_contains($kdBrg, '…')) {
                    $kdBrg = '';
                }
                $bmnGroup = trim($item['bmn_group'] ?? ($catInfo['bmn_group'] ?? ''));
                if (str_contains($bmnGroup, '…')) {
                    $bmnGroup = '';
                }
                $kdRincian = trim($item['kd_rincian'] ?? ($catInfo['kd_rincian'] ?? ''));
                $code = $kdRincian !== '' ? $kdRincian : sprintf('ATK-%04d', $idx + 1);
                $note = trim($item['note'] ?? '');

                $descParts = [];
                if (count($units) > 1) {
                    $descParts[] = 'Pilihan Satuan: ' . implode(' / ', $units);
                } else {
                    $descParts[] = 'Satuan Standar: ' . $primaryUnit;
                }
                if ($kdBrg !== '') {
                    $descParts[] = 'BMN: ' . $kdBrg . ($bmnGroup ? " ({$bmnGroup})" : '');
                }
                if ($kdRincian !== '') {
                    $descParts[] = 'Kode Rincian: ' . $kdRincian;
                }
                if ($note !== '') {
                    $descParts[] = 'Ket: ' . $note;
                }
                $description = implode(' | ', $descParts);

                $legacyCode = sprintf('ATK-%04d', $idx + 1);

                // Cari apakah barang dengan nama & kategori yang sama atau legacy code sudah ada
                $normName = preg_replace('/\s+/', ' ', $name);
                $existing = Product::where('category_id', $catId)
                    ->where(function ($q) use ($name, $normName) {
                        $q->where('name', $name)->orWhere('name', $normName);
                    })
                    ->first();

                if (!$existing) {
                    $existing = Product::where('code', $legacyCode)->first();
                }

                if ($existing) {
                    $existing->update([
                        'category_id' => $catId,
                        'code' => $code,
                        'bmn_code' => $kdBrg ?: $existing->bmn_code,
                        'bmn_group' => $bmnGroup ?: $existing->bmn_group,
                        'name' => $name,
                        'unit' => $existing->stock > 0 ? $existing->unit : $primaryUnit,
                        'available_units' => $availableUnitsStr,
                        'description' => $existing->description ?: $description,
                    ]);
                } else {
                    DB::table('products')->insert([
                        'category_id' => $catId,
                        'code' => $code,
                        'bmn_code' => $kdBrg ?: null,
                        'bmn_group' => $bmnGroup ?: null,
                        'name' => $name,
                        'unit' => $primaryUnit,
                        'available_units' => $availableUnitsStr,
                        'stock' => 0,
                        'minimum_stock' => 5,
                        'purchase_price' => 0,
                        'description' => $description,
                        'created_at' => $seededTimestamp,
                        'updated_at' => $seededTimestamp,
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed for data sync
    }
};
