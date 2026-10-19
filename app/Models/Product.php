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
    ];

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
