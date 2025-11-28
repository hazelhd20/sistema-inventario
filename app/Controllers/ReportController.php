<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Movement;
use App\Models\Product;

class ReportController extends Controller
{
    public function index(): void
    {
        require_login();

        $reportType = $_GET['report'] ?? 'inventory';
        $dateRange = $_GET['range'] ?? 'month';
        
        // Fechas específicas para el reporte de movimientos
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        $products = Product::all(null, null, true);
        $lowStock = Product::lowStock();

        $inventoryValue = array_reduce($products, fn ($carry, $p) => $carry + ($p['price'] * $p['stock_quantity']), 0);
        $inventoryCost = array_reduce($products, fn ($carry, $p) => $carry + ($p['cost'] * $p['stock_quantity']), 0);

        $productsByCategory = [];
        foreach ($products as $product) {
            $category = $product['category'] ?: 'General';
            $productsByCategory[$category] = ($productsByCategory[$category] ?? 0) + 1;
        }

        $valueByCategory = [];
        foreach ($products as $product) {
            $category = $product['category'] ?: 'General';
            $valueByCategory[$category] = ($valueByCategory[$category] ?? 0) + ($product['price'] * $product['stock_quantity']);
        }

        // Movimientos según el tipo de filtro
        if ($dateFrom || $dateTo) {
            // Filtro por fechas específicas
            $filters = [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ];
            $movementStats = Movement::statsByDateRange($dateFrom ?: null, $dateTo ?: null);
            $movements = Movement::filtered($filters);
            $totalsByProduct = Movement::totalsByProduct($dateFrom ?: null, $dateTo ?: null);
        } else {
            // Filtro por rango predefinido
            $filters = [
                'date_range' => $dateRange,
            ];
            $movementStats = Movement::stats($dateRange);
            $movements = Movement::filtered($filters);
            $totalsByProduct = [];
        }

        // Fecha y hora de generación del reporte
        $reportGeneratedAt = date('d/m/Y H:i:s');

        $this->render('reports/index', [
            'reportType' => $reportType,
            'dateRange' => $dateRange,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'products' => $products,
            'lowStock' => $lowStock,
            'productsByCategory' => $productsByCategory,
            'valueByCategory' => $valueByCategory,
            'inventoryValue' => $inventoryValue,
            'inventoryCost' => $inventoryCost,
            'movementStats' => $movementStats,
            'movements' => $movements,
            'totalsByProduct' => $totalsByProduct,
            'reportGeneratedAt' => $reportGeneratedAt,
            'message' => flash('success'),
        ]);
    }
}
