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

        $now = now();
        $seededTimestamp = '2026-01-01 08:00:00';

        // 1. Bulk insert/update categories
        $catRows = [];
        $categoryInfoMap = [];
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

            $catRows[] = [
                'name' => $cName,
                'kode_barang' => $kdBrg ?: null,
                'kd_rincian' => $kdRincian ?: null,
                'description' => $desc,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $categoryInfoMap[$cName] = [
                'kd_brg' => $kdBrg,
                'kd_rincian' => $kdRincian,
                'bmn_group' => $bmnGroup,
            ];
        }

        DB::table('categories')->upsert($catRows, ['name'], ['kode_barang', 'kd_rincian', 'description', 'updated_at']);
        $categoryMap = DB::table('categories')->pluck('id', 'name')->toArray();

        // 2. Prepare all 572 product rows for bulk insert
        $productRows = [];
        foreach ($data['items'] as $idx => $item) {
            $catName = trim($item['category']);
            $catId = $categoryMap[$catName] ?? null;
            if (!$catId) {
                $catId = DB::table('categories')->insertGetId([
                    'name' => $catName,
                    'description' => "Kategori Standar ATK ({$catName})",
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $categoryMap[$catName] = $catId;
            }

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

            $productRows[] = [
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
            ];
        }

        // Insert in chunks of 100 for high-speed network execution if table is empty
        if (DB::table('products')->count() === 0) {
            foreach (array_chunk($productRows, 100) as $chunk) {
                DB::table('products')->insert($chunk);
            }
        }
    }
}
