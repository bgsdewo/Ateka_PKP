<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;

class BarangMasukController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name', 'asc')
            ->get();

        $transactions = StockTransaction::with(['product.category', 'user'])
            ->where('type', 'masuk')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('inventory.barang-masuk', compact(
            'products',
            'transactions'
        ));
    }
}
