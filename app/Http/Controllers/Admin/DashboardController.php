<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;

class DashboardController extends Controller
{
    public function index()
    {
        Order::expireOverdue();

        return view('admin.dashboard', [
            'needShipping' => Order::where('order_status', 'menunggu_ongkir')->count(),
            'needVerify'   => Order::where('payment_status', 'menunggu_verifikasi')->count(),
            'inProgress'   => Order::whereIn('order_status', ['diproses', 'dikirim'])->count(),
            'productCount' => Product::count(),
            'lowStock'     => ProductVariant::where('stock', '>', 0)->where('stock', '<=', ProductVariant::LOW_STOCK)->count(),
            'outStock'     => ProductVariant::where('stock', 0)->count(),
            'monthRevenue' => Order::where('payment_status', 'lunas')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->get()->sum(fn ($o) => $o->grandTotal()),
            'latestOrders' => Order::latest('order_date')->take(6)->get(),
        ]);
    }
}
