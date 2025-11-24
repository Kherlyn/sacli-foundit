<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Services\AdminNotificationService;
use App\Services\ItemService;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
  public function __construct(
    private ItemService $itemService,
    private StatisticsService $statisticsService,
    private AdminNotificationService $adminNotificationService
  ) {}

  /**
   * Display the admin dashboard with statistics overview.
   */
  public function dashboard()
  {
    $statistics = $this->statisticsService->getOverviewStats();
    $categoryStats = $this->statisticsService->getItemCountsByCategory();
    $trendData = $this->statisticsService->getSubmissionTrends(30);
    $successMetrics = $this->statisticsService->getSuccessRateMetrics();
    $performanceMetrics = $this->statisticsService->getPerformanceMetrics();
    $recentActivity = $this->statisticsService->getRecentActivity(7);
    $comparisonStats = $this->statisticsService->getComparisonStats(30, 30);
    $recentItems = $this->itemService->getRecentItems(5);
    $pendingCount = Item::pending()->count();
    $itemsRequiringAttention = $this->itemService->getItemsRequiringAttention(7);

    // Additional statistics that might be expected by the view
    $statistics['items_this_month'] = Item::whereMonth('created_at', now()->month)
      ->whereYear('created_at', now()->year)
      ->count();
    $statistics['items_today'] = Item::whereDate('created_at', today())->count();
    $statistics['items_this_week'] = Item::whereBetween('created_at', [
      now()->startOfWeek(),
      now()->endOfWeek()
    ])->count();

    // Get admin notifications
    /** @var \App\Models\Admin $admin */
    $admin = auth()->guard('admin')->user();
    $unreadNotificationCount = $this->adminNotificationService->getUnreadNotificationCount($admin);
    $recentNotifications = $this->adminNotificationService->getRecentNotifications($admin, 5);

    return view('admin.dashboard', compact(
      'statistics',
      'categoryStats',
      'trendData',
      'successMetrics',
      'performanceMetrics',
      'recentActivity',
      'comparisonStats',
      'recentItems',
      'pendingCount',
      'itemsRequiringAttention',
      'unreadNotificationCount',
      'recentNotifications'
    ));
  }

  /**
   * Display items awaiting verification.
   */
  public function pendingItems(Request $request)
  {
    $perPage = $request->get('per_page', 15);
    $pendingItems = Item::pending()
      ->with(['category', 'user', 'images'])
      ->orderBy('created_at', 'asc')
      ->paginate($perPage);

    return view('admin.pending-items', compact('pendingItems'));
  }

  /**
   * Verify (approve or reject) an item submission.
   */
  public function verify(Request $request, Item $item)
  {
    $request->validate([
      'action' => ['required', Rule::in(['approve', 'reject'])],
      'admin_notes' => 'nullable|string|max:1000',
    ]);

    $action = $request->input('action');
    $adminNotes = $request->input('admin_notes');

    try {
      if ($action === 'approve') {
        $this->itemService->verifyItem($item, $adminNotes);
        $message = 'Item has been approved and is now publicly visible.';
      } else {
        $this->itemService->rejectItem($item, $adminNotes);
        $message = 'Item has been rejected.';
      }

      return response()->json([
        'success' => true,
        'message' => $message,
        'item' => [
          'id' => $item->id,
          'status' => $item->fresh()->status,
          'admin_notes' => $item->fresh()->admin_notes,
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'An error occurred while processing the item.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Display and manage item categories.
   */
  public function categories()
  {
    $categories = Category::withCount('items')
      ->orderBy('name')
      ->get();

    return view('admin.categories', compact('categories'));
  }

  /**
   * Store a new category.
   */
  public function storeCategory(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:100|unique:categories,name',
      'description' => 'nullable|string|max:500',
      'icon' => 'nullable|string|max:50',
      'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);

    try {
      $category = Category::create([
        'name' => $request->input('name'),
        'description' => $request->input('description'),
        'icon' => $request->input('icon'),
        'color' => $request->input('color', '#10B981'), // Default green color
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Category created successfully.',
        'category' => $category
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'An error occurred while creating the category.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Update an existing category.
   */
  public function updateCategory(Request $request, Category $category)
  {
    $request->validate([
      'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
      'description' => 'nullable|string|max:500',
      'icon' => 'nullable|string|max:50',
      'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
    ]);

    try {
      $category->update([
        'name' => $request->input('name'),
        'description' => $request->input('description'),
        'icon' => $request->input('icon'),
        'color' => $request->input('color', '#10B981'),
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Category updated successfully.',
        'category' => $category->fresh()
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'An error occurred while updating the category.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Delete a category.
   */
  public function deleteCategory(Category $category)
  {
    try {
      // Check if category has items
      if ($category->items()->count() > 0) {
        return response()->json([
          'success' => false,
          'message' => 'Cannot delete category that has items associated with it.'
        ], 400);
      }

      $category->delete();

      return response()->json([
        'success' => true,
        'message' => 'Category deleted successfully.'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'An error occurred while deleting the category.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Display all items with admin filters.
   */
  public function items(Request $request)
  {
    $filters = [
      'query' => $request->get('query'),
      'status' => $request->get('status'),
      'type' => $request->get('type'),
      'category_id' => $request->get('category_id'),
      'course' => $request->get('course'),
      'year' => $request->get('year'),
    ];

    $perPage = $request->get('per_page', 15);
    $items = $this->itemService->getAdminItems($filters, $perPage);
    $categories = Category::orderBy('name')->get();

    return view('admin.items', compact('items', 'categories', 'filters'));
  }

  /**
   * Show detailed view of an item for admin.
   */
  public function showItem(Item $item)
  {
    $item->load(['category', 'user', 'images']);

    // Get other recent items (not similar, just other items)
    $otherItems = Item::where('id', '!=', $item->id)
      ->with(['category', 'user', 'images'])
      ->orderBy('created_at', 'desc')
      ->limit(4)
      ->get();

    return view('admin.item-detail', compact('item', 'otherItems'));
  }

  /**
   * Bulk actions on items.
   */
  public function bulkAction(Request $request)
  {
    $request->validate([
      'action' => ['required', Rule::in(['approve', 'reject', 'delete'])],
      'item_ids' => 'required|array|min:1',
      'item_ids.*' => 'exists:items,id',
      'admin_notes' => 'nullable|string|max:1000',
    ]);

    $action = $request->input('action');
    $itemIds = $request->input('item_ids');
    $adminNotes = $request->input('admin_notes');

    try {
      $count = 0;

      if ($action === 'approve') {
        $count = $this->itemService->bulkUpdateStatus($itemIds, 'verified', $adminNotes);
        $message = "Successfully approved {$count} items.";
      } elseif ($action === 'reject') {
        $count = $this->itemService->bulkUpdateStatus($itemIds, 'rejected', $adminNotes);
        $message = "Successfully rejected {$count} items.";
      } elseif ($action === 'delete') {
        $items = Item::whereIn('id', $itemIds)->get();
        foreach ($items as $item) {
          $this->itemService->deleteItem($item);
          $count++;
        }
        $message = "Successfully deleted {$count} items.";
      }

      return response()->json([
        'success' => true,
        'message' => $message,
        'count' => $count
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'An error occurred while processing the bulk action.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Display detailed statistics page with charts and visualizations.
   */
  public function statistics()
  {
    $overviewStats = $this->statisticsService->getOverviewStats();
    $categoryStats = $this->statisticsService->getItemCountsByCategory();
    $submissionTrends = $this->statisticsService->getSubmissionTrends(90);
    $verificationTrends = $this->statisticsService->getVerificationTrends(90);
    $monthlyStats = $this->statisticsService->getMonthlyStats();
    $successMetrics = $this->statisticsService->getSuccessRateMetrics();
    $topCategories = $this->statisticsService->getTopCategories(10);
    $locationStats = $this->statisticsService->getLocationStats();
    $performanceMetrics = $this->statisticsService->getPerformanceMetrics();
    $comparisonStats = $this->statisticsService->getComparisonStats(30, 30);

    return view('admin.statistics', compact(
      'overviewStats',
      'categoryStats',
      'submissionTrends',
      'verificationTrends',
      'monthlyStats',
      'successMetrics',
      'topCategories',
      'locationStats',
      'performanceMetrics',
      'comparisonStats'
    ));
  }

  /**
   * Get statistics data for charts (AJAX endpoint).
   */
  public function statisticsData(Request $request)
  {
    $type = $request->get('type', 'overview');
    $days = $request->get('days', 30);

    try {
      $data = [];

      switch ($type) {
        case 'overview':
          $data = $this->statisticsService->getOverviewStats();
          break;

        case 'categories':
          $data = $this->statisticsService->getItemCountsByCategory();
          break;

        case 'submission_trends':
          $data = $this->statisticsService->getSubmissionTrends($days);
          break;

        case 'verification_trends':
          $data = $this->statisticsService->getVerificationTrends($days);
          break;

        case 'monthly_stats':
          $data = $this->statisticsService->getMonthlyStats();
          break;

        case 'success_metrics':
          $data = $this->statisticsService->getSuccessRateMetrics();
          break;

        case 'top_categories':
          $data = $this->statisticsService->getTopCategories(10);
          break;

        case 'location_stats':
          $data = $this->statisticsService->getLocationStats();
          break;

        case 'performance_metrics':
          $data = $this->statisticsService->getPerformanceMetrics();
          break;

        case 'comparison_stats':
          $currentDays = $request->get('current_days', 30);
          $previousDays = $request->get('previous_days', 30);
          $data = $this->statisticsService->getComparisonStats($currentDays, $previousDays);
          break;

        default:
          return response()->json([
            'success' => false,
            'message' => 'Invalid statistics type requested.'
          ], 400);
      }

      return response()->json([
        'success' => true,
        'data' => $data
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'An error occurred while fetching statistics.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Export statistics data.
   */
  public function exportStatistics(Request $request)
  {
    $format = $request->get('format', 'csv');
    $options = [
      'include_categories' => $request->boolean('include_categories', true),
      'include_trends' => $request->boolean('include_trends', true),
      'include_monthly' => $request->boolean('include_monthly', true),
      'include_locations' => $request->boolean('include_locations', false),
      'include_recent' => $request->boolean('include_recent', true),
      'trend_days' => $request->get('trend_days', 30),
      'recent_days' => $request->get('recent_days', 7),
    ];

    try {
      $exportData = $this->statisticsService->exportStatisticalData($options);

      if ($format === 'json') {
        return response()->json($exportData);
      }

      // For CSV format, we'll return a flattened CSV structure
      $csvData = [];
      $csvData[] = ['Section', 'Metric', 'Value'];

      // Overview statistics
      foreach ($exportData['overview'] as $key => $value) {
        $csvData[] = ['Overview', ucwords(str_replace('_', ' ', $key)), $value];
      }

      // Success metrics
      foreach ($exportData['success_metrics'] as $key => $value) {
        $csvData[] = ['Success Metrics', ucwords(str_replace('_', ' ', $key)), $value];
      }

      // Category statistics
      if (isset($exportData['categories'])) {
        foreach ($exportData['categories'] as $category) {
          $csvData[] = ['Categories', $category->name . ' - Total Items', $category->items_count];
          $csvData[] = ['Categories', $category->name . ' - Verified Items', $category->verified_items_count];
        }
      }

      $filename = 'sacli-foundit-statistics-' . now()->format('Y-m-d') . '.csv';

      $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
      ];

      $callback = function () use ($csvData) {
        $file = fopen('php://output', 'w');
        foreach ($csvData as $row) {
          fputcsv($file, $row);
        }
        fclose($file);
      };

      return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'An error occurred while exporting statistics.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get admin notifications (AJAX endpoint).
   */
  public function notifications(Request $request)
  {
    $limit = $request->get('limit', 10);
    $unreadOnly = $request->boolean('unread_only', false);

    /** @var \App\Models\Admin $admin */
    $admin = auth()->guard('admin')->user();

    $notifications = $unreadOnly
      ? $admin->unreadNotifications()->limit($limit)->get()
      : $admin->notifications()->limit($limit)->get();

    $unreadCount = $this->adminNotificationService->getUnreadNotificationCount($admin);

    return response()->json([
      'success' => true,
      'notifications' => $notifications,
      'unread_count' => $unreadCount,
    ]);
  }

  /**
   * Mark notifications as read.
   */
  public function markNotificationsRead(Request $request)
  {
    $request->validate([
      'notification_ids' => 'nullable|array',
      'notification_ids.*' => 'uuid|exists:notifications,id',
      'mark_all' => 'boolean',
    ]);

    try {
      /** @var \App\Models\Admin $admin */
      $admin = auth()->guard('admin')->user();
      $notificationIds = $request->get('notification_ids', []);

      if ($request->boolean('mark_all')) {
        $this->adminNotificationService->markNotificationsAsRead($admin);
        $message = 'All notifications marked as read.';
      } else {
        $this->adminNotificationService->markNotificationsAsRead($admin, $notificationIds);
        $count = count($notificationIds);
        $message = "Marked {$count} notifications as read.";
      }

      $unreadCount = $this->adminNotificationService->getUnreadNotificationCount($admin);

      return response()->json([
        'success' => true,
        'message' => $message,
        'unread_count' => $unreadCount,
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'An error occurred while updating notifications.',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Send test notification (for testing purposes).
   */
  public function sendTestNotification(Request $request)
  {
    $request->validate([
      'type' => ['required', Rule::in(['new_submission', 'queue_alert', 'system_event'])],
    ]);

    try {
      $type = $request->get('type');

      switch ($type) {
        case 'new_submission':
          // Create a mock item for testing
          $item = Item::with(['category', 'user'])->first();
          if ($item) {
            $this->adminNotificationService->notifyNewItemSubmission($item);
          }
          break;

        case 'queue_alert':
          $pendingCount = Item::pending()->count();
          $this->adminNotificationService->sendPendingQueueAlert($pendingCount);
          break;

        case 'system_event':
          $this->adminNotificationService->notifySystemEvent(
            'test',
            'Test System Event',
            'This is a test system event notification.',
            ['test_data' => 'test_value'],
            'normal'
          );
          break;
      }

      return response()->json([
        'success' => true,
        'message' => 'Test notification sent successfully.',
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'An error occurred while sending test notification.',
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
