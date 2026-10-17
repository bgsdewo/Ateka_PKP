<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Lembar2AtkCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/lembar2_atk_catalog.json');
        if (!file_exists($jsonPath)) {
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data || empty($data['categories']) || empty($data['items'])) {
            return;
        }

        DB::transaction(function () use ($data) {
            // 1. Sinkronkan seluruh 56 Kategori dari Lembar 2 LIST ATK
            $categoryMap = [];
            $validCategoryNames = [];

            foreach ($data['categories'] as $catData) {
                $cName = trim($catData['name']);
                $kdBrg = trim($catData['kd_brg'] ?? '');
                $bmnGroup = trim($catData['bmn_group'] ?? '');

                $desc = '';
                if ($kdBrg !== '' && !str_contains($kdBrg, '…')) {
                    $desc = "Kode Barang BMN: {$kdBrg}" . ($bmnGroup !== '' ? " ({$bmnGroup})" : '');
                } else {
                    $desc = "Kategori Standar ATK ({$cName})";
                }

                $category = Category::updateOrCreate(
                    ['name' => $cName],
                    ['description' => $desc]
                );

                $categoryMap[$cName] = $category->id;
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

                $kdBrg = trim($item['kd_brg'] ?? '');
                if (str_contains($kdBrg, '…')) {
                    $kdBrg = '';
                }
                $bmnGroup = trim($item['bmn_group'] ?? '');
                if (str_contains($bmnGroup, '…')) {
                    $bmnGroup = '';
                }
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
                if ($note !== '') {
                    $descParts[] = 'Ket: ' . $note;
                }
                $description = implode(' | ', $descParts);

                $code = sprintf('ATK-%04d', $idx + 1);

                // Cari apakah barang dengan nama & kategori yang sama atau kode yang sama sudah ada
                $existing = Product::where('name', $name)
                    ->where('category_id', $catId)
                    ->first();

                if (!$existing) {
                    $existing = Product::where('code', $code)->first();
                }

                if ($existing) {
                    $existing->update([
                        'category_id' => $catId,
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
}
