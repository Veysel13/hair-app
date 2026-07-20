<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private function business(): Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->firstOrFail();
    }

    public function index()
    {
        $business      = $this->business();
        $products      = $business->products()->orderBy('category')->orderBy('name')->get();
        $lowStockCount = $products->filter->isLowStock()->count();

        return view('admin.products.index', compact('business', 'products', 'lowStockCount'));
    }

    public function create()
    {
        $business = $this->business();
        return view('admin.products.create', compact('business'));
    }

    public function store(Request $request)
    {
        $business = $this->business();
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'brand'          => 'nullable|string|max:100',
            'sku'            => 'nullable|string|max:100',
            'category'       => 'nullable|string|max:100',
            'unit'           => 'required|string|max:20',
            'current_stock'  => 'required|numeric|min:0',
            'min_stock_level'=> 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price'     => 'nullable|numeric|min:0',
            'is_for_sale'    => 'boolean',
            'notes'          => 'nullable|string|max:500',
        ]);

        $product = $business->products()->create($data);

        // İlk stok hareketi
        if ($data['current_stock'] > 0) {
            StockMovement::create([
                'business_id'=> $business->id,
                'product_id' => $product->id,
                'type'       => 'in',
                'quantity'   => $data['current_stock'],
                'unit_price' => $data['purchase_price'] ?? null,
                'notes'      => 'Başlangıç stoğu',
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Ürün eklendi.');
    }

    public function edit(Product $product)
    {
        $business = $this->business();
        $product->load('stockMovements');
        return view('admin.products.edit', compact('business', 'product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'brand'          => 'nullable|string|max:100',
            'category'       => 'nullable|string|max:100',
            'unit'           => 'required|string|max:20',
            'min_stock_level'=> 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price'     => 'nullable|numeric|min:0',
            'is_for_sale'    => 'boolean',
            'is_active'      => 'boolean',
            'notes'          => 'nullable|string|max:500',
        ]);

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Ürün güncellendi.');
    }

    public function addStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity'   => 'required|numeric|min:0.001',
            'unit_price' => 'nullable|numeric|min:0',
            'notes'      => 'nullable|string|max:255',
        ]);

        StockMovement::create([
            'business_id'=> $product->business_id,
            'product_id' => $product->id,
            'type'       => 'in',
            'quantity'   => $data['quantity'],
            'unit_price' => $data['unit_price'] ?? null,
            'notes'      => $data['notes'] ?? 'Stok girişi',
            'created_by' => Auth::id(),
        ]);

        $product->increment('current_stock', $data['quantity']);
        return back()->with('success', 'Stok eklendi.');
    }
}
