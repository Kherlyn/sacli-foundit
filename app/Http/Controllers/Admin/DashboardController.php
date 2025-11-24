<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\ItemService;
use App\Services\StatisticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
  public function __construct(
    private ItemService $itemService,
    private StatisticsService $statisticsService
  ) {}

  /**
   * Display the admin dashboard.
   */
  public function index(): View
  {
    $statistics = $this->statisticsService->getOverviewStats();
    $categoryStats = $this->statisticsService->getItemCountsByCategory();
    $recentItems = $this->itemService->getRecentItems(5);
    $pendingCount = Item::pending()->count();
    $itemsRequiringAttention = $this->itemService->getItemsRequiringAttention(7);

    // Additional statistics that might be expected by the view
    $statistics['items_this_month'] = Item::whereMonth('created_at', now()->month)
      ->whereYear('created_at', now()->year)
      ->count();

    return view('admin.dashboard', compact(
      'statistics',
      'categoryStats',
      'recentItems',
      'pendingCount',
      'itemsRequiringAttention'
    ));
  }
}
