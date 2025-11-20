<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Movement;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(): void
    {
        require_login();

        $stats = Product::stats();
        $movementStats = Movement::stats();
        $lowStock = Product::lowStock();

        $this->render('dashboard/index', [
            'stats' => $stats,
            'movementStats' => $movementStats,
            'lowStock' => $lowStock,
            'recentProducts' => Product::recent(),
            'recentMovements' => Movement::latest(),
        ]);
    }
}
