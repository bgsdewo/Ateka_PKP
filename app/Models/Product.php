<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'bmn_code',
        'bmn_group',
        'name',
        'unit',
        'available_units',
        'stock',
        'minimum_stock',
        'purchase_price',
        'description',
        'khusus_subdit',
        'image',
    ];

    protected $casts = [
        'khusus_subdit' => 'boolean',
    ];

    /** Kata kunci nama barang yang otomatis ditandai "Khusus Perwakilan Subdit". */
    public const KHUSUS_SUBDIT_KEYWORDS = [
        'pembolong kertas',
        'toner',
        'epson ink printer',
        'cannon blanck ink',
        'canon black ink',
        'canon blank ink',
    ];

    public static function nameIsKhususSubdit(?string $name): bool
    {
        $name = mb_strtolower((string) $name);
        foreach (self::KHUSUS_SUBDIT_KEYWORDS as $kw) {
            if ($name !== '' && str_contains($name, $kw)) {
                return true;
            }
        }
        return false;
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (file_exists(public_path($this->image))) {
                return $this->image;
            }
            if (str_starts_with($this->image, 'storage/') && file_exists(storage_path('app/public/' . str_replace('storage/', '', $this->image)))) {
                return $this->image;
            }
        }

        $slug = $this->default_image_slug;
        $path = 'img/atk/' . $slug . '.svg';
        if (file_exists(public_path($path))) {
            return $path;
        }

        return 'img/atk/general.svg';
    }

    public function getDefaultImageSlugAttribute(): string
    {
        $catName = strtolower($this->category->name ?? '');
        $itemName = strtolower($this->name ?? '');

        // 1. Specific Isi Staples vs Stapler
        if (str_contains($catName, 'isi staples') || str_contains($itemName, 'isi staples') || str_contains($itemName, 'isi stapler')) {
            return 'isi-staples';
        }

        $map = [
            'amplop' => 'amplop',
            'bak stempel' => 'bak-stempel',
            'baterai' => 'baterai',
            'binder clip' => 'binder-clip',
            'book stand' => 'book-stand',
            'box file' => 'box-file',
            'buku' => 'buku',
            'business file' => 'business-file',
            'clear holder' => 'clear-holder',
            'converter / usb hub' => 'converter-usb-hub',
            'cutter' => 'cutter',
            'double tape' => 'double-tape',
            'elevated tray' => 'elevated-tray',
            'flashdisk' => 'flashdisk',
            'gunting' => 'gunting',
            'hardisk eksternal' => 'hardisk-eksternal',
            'isi staples' => 'isi-staples',
            'kalkulator' => 'kalkulator',
            'kertas' => 'kertas',
            'keyboard' => 'keyboard',
            'label sticker' => 'label-sticker',
            'lakban' => 'lakban',
            'laser pointer' => 'laser-pointer',
            'lem' => 'lem',
            'map' => 'map',
            'mouse wireless' => 'mouse-wireless',
            'ordner' => 'ordner',
            'paper clip' => 'paper-clip',
            'pembolong kertas' => 'pembolong-kertas',
            'penggaris' => 'penggaris',
            'penghapus' => 'penghapus',
            'pensil' => 'pensil',
            'pita' => 'pita',
            'post it' => 'post-it',
            'pulpen' => 'pulpen',
            'rautan pensil' => 'rautan-pensil',
            'recorder' => 'recorder',
            'ring o-bin' => 'ring-o-bin',
            'selotip' => 'selotip',
            'sheet protector' => 'sheet-protector',
            'spidol' => 'spidol',
            'stabilo' => 'stabilo',
            'stapler' => 'stapler',
            'tape cutter' => 'tape-cutter',
            'tape dispenser' => 'tape-dispenser',
            'tinta brother' => 'tinta-brother',
            'tinta canon' => 'tinta-canon',
            'tinta epson' => 'tinta-epson',
            'tinta hp' => 'tinta-hp',
            'tinta pulpen' => 'tinta-pulpen',
            'tipe ex' => 'tipe-ex',
            'toner printer' => 'toner-printer',
            'transparant sheet' => 'transparant-sheet',
            'usb' => 'usb',
            'v-tech desk organizer' => 'v-tech-desk-organizer',
            'v-tech round pencil cup' => 'v-tech-round-pencil-cup',
        ];

        foreach ($map as $key => $slug) {
            if (str_contains($catName, $key) || str_contains($itemName, $key)) {
                return $slug;
            }
        }

        return 'general';
    }

    public function getSizeSpecAttribute(): ?array
    {
        return null;
    }

    public function getUnitListAttribute(): array
    {
        $units = [];
        if (!empty($this->available_units)) {
            foreach (explode(',', $this->available_units) as $u) {
                $uTrim = trim($u);
                if ($uTrim !== '' && !in_array($uTrim, $units)) {
                    $units[] = $uTrim;
                }
            }
        }
        if (!empty($this->unit)) {
            $primary = trim($this->unit);
            if ($primary !== '' && !in_array($primary, $units)) {
                array_unshift($units, $primary);
            }
        }
        return !empty($units) ? $units : ['Buah'];
    }

    /**
     * Kode Barang untuk laporan: mengikuti Kode Barang pada KATEGORI (mis. 1010302004),
     * bukan Kode Rincian per barang. Cadangan: kode BMN barang, lalu kode barang itu sendiri.
     */
    public function getKodeBarangLaporanAttribute(): string
    {
        return $this->category?->kode_barang
            ?: ($this->bmn_code ?: ($this->code ?: '-'));
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function requestDetails()
    {
        return $this->hasMany(RequestDetail::class);
    }

    public function getInitialStockTransactionAttribute()
    {
        if (!$this->relationLoaded('stockTransactions')) {
            return $this->stockTransactions()->where('type', 'masuk')->orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->first();
        }

        $masukTransactions = $this->stockTransactions->where('type', 'masuk');

        $startDate = request('start_date');
        $endDate = request('end_date');
        $year = request('year');
        $month = request('month');

        if ($startDate && $endDate) {
            $minDate = min($startDate, $endDate);
            $maxDate = max($startDate, $endDate);
            $match = $masukTransactions->first(function ($t) use ($minDate, $maxDate) {
                $d = $t->transaction_date ? \Carbon\Carbon::parse($t->transaction_date)->format('Y-m-d') : null;
                return $d && $d >= $minDate && $d <= $maxDate;
            });
            if ($match) {
                return $match;
            }
        } elseif ($startDate) {
            $match = $masukTransactions->first(function ($t) use ($startDate) {
                $d = $t->transaction_date ? \Carbon\Carbon::parse($t->transaction_date)->format('Y-m-d') : null;
                return $d && $d === $startDate;
            });
            if ($match) {
                return $match;
            }
        } elseif ($endDate) {
            $match = $masukTransactions->first(function ($t) use ($endDate) {
                $d = $t->transaction_date ? \Carbon\Carbon::parse($t->transaction_date)->format('Y-m-d') : null;
                return $d && $d === $endDate;
            });
            if ($match) {
                return $match;
            }
        } else {
            if ($year && $month) {
                $match = $masukTransactions->first(function ($t) use ($year, $month) {
                    if (!$t->transaction_date) return false;
                    $dt = \Carbon\Carbon::parse($t->transaction_date);
                    return $dt->year == $year && $dt->month == (int) $month;
                });
                if ($match) {
                    return $match;
                }
            } elseif ($year) {
                $match = $masukTransactions->first(function ($t) use ($year) {
                    if (!$t->transaction_date) return false;
                    return \Carbon\Carbon::parse($t->transaction_date)->year == $year;
                });
                if ($match) {
                    return $match;
                }
            } elseif ($month) {
                $match = $masukTransactions->first(function ($t) use ($month) {
                    if (!$t->transaction_date) return false;
                    return \Carbon\Carbon::parse($t->transaction_date)->month == (int) $month;
                });
                if ($match) {
                    return $match;
                }
            }
        }

        return $masukTransactions->first();
    }

    public function getInitialNoFakturAttribute(): ?string
    {
        return $this->initial_stock_transaction?->no_faktur ?? '';
    }

    public function getInitialUraianAttribute(): ?string
    {
        return $this->initial_stock_transaction?->uraian ?? '';
    }

    public function getInitialTransactionDateAttribute(): ?string
    {
        $tx = $this->initial_stock_transaction;
        if ($tx && $tx->transaction_date) {
            return \Carbon\Carbon::parse($tx->transaction_date)->format('Y-m-d');
        }
        return null;
    }

    public function getInitialTransactionDateFormattedAttribute(): string
    {
        $tx = $this->initial_stock_transaction;
        if ($tx && $tx->transaction_date) {
            return \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y');
        }
        return '-';
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'Stok Belum Tersedia';
        }
        return $this->stock < 10 ? 'Akan Habis' : 'Aman';
    }
}
