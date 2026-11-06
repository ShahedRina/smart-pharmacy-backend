<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Order;

class AdminController extends Controller
{
    public function dashboard()
    {
        $usersCount = User::where('role', 'user')->count();

        $deliveryCount = User::where('role', 'delivery')->count();

        $medicinesCount = Medicine::count();

        $categoriesCount = Category::count();

        $ordersCount = Order::count();

        return response()->json([
            'users_count' => $usersCount,
            'delivery_count' => $deliveryCount,
            'medicines_count' => $medicinesCount,
            'categories_count' => $categoriesCount,
            'orders_count' => $ordersCount,
        ]);
    }
}