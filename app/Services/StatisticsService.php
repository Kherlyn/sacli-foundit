<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class StatisticsService
{
  /**
   * Get total counts for dashboard overview.
   */
  public function getOverviewStats(): array
  {
    return [
      'total_items' => Item::count(),
      'verified_items' => Item::verified()->count(),
      'pending_items' => Item::pending()->count(),
      'rejected_items' => Item::where('status', 'rejected')->count(),
      'resolved_items' => Item::where('status', 'resolved')->count(),
      'lost_items' => Item::where('type', 'lost')->count(),
      'found_items' => Item::where('type', 'found')->count(),
      'total_categories' => Category::count(),
      'total_users' => User::count(),
    ];
  }

  /**
   * Get item counts by category.
   */
  public function getItemCountsByCategory(): Collection
  {
    return Category::withCount([
      'items',
      'items as verified_items_count' => function ($query) {
        $query->where('status', 'verified');
      },
      'items as pending_items_count' => function ($query) {
        $query->where('status', 'pending');
      },
      'items as resolved_items_count' => function ($query) {
        $query->where('status', 'resolved');
      },
      'items as lost_items_count' => function ($query) {
        $query->where('type', 'lost');
      },
      'items as found_items_count' => function ($query) {
        $query->where('type', 'found');
      }
    ])->get();
  }

  /**
   * Get submission trends over time.
   */
  public function getSubmissionTrends(int $days = 30): Collection
  {
    $startDate = Carbon::now()->subDays($days);

    return Item::select(
      DB::raw('DATE(created_at) as date'),
      DB::raw('COUNT(*) as total_submissions'),
      DB::raw('SUM(CASE WHEN type = "lost" THEN 1 ELSE 0 END) as lost_submissions'),
      DB::raw('SUM(CASE WHEN type = "found" THEN 1 ELSE 0 END) as found_submissions'),
      DB::raw('SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) as verified_submissions'),
      DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_submissions')
    )
      ->where('created_at', '>=', $startDate)
      ->groupBy(DB::raw('DATE(created_at)'))
      ->orderBy('date')
      ->get();
  }

  /**
   * Get verification trends over time.
   */
  public function getVerificationTrends(int $days = 30): Collection
  {
    $startDate = Carbon::now()->subDays($days);

    return Item::select(
      DB::raw('DATE(verified_at) as date'),
      DB::raw('COUNT(*) as verified_count'),
      DB::raw('SUM(CASE WHEN type = "lost" THEN 1 ELSE 0 END) as lost_verified'),
      DB::raw('SUM(CASE WHEN type = "found" THEN 1 ELSE 0 END) as found_verified')
    )
      ->whereNotNull('verified_at')
      ->where('verified_at', '>=', $startDate)
      ->groupBy(DB::raw('DATE(verified_at)'))
      ->orderBy('date')
      ->get();
  }

  /**
   * Get success rate metrics and resolution tracking.
   */
  public function getSuccessRateMetrics(int $days = null): array
  {
    $query = Item::query();
    if ($days) {
      $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    $totalSubmissions = $query->count();
    $totalVerified = (clone $query)->whereIn('status', ['verified', 'resolved'])->count();
    $totalResolved = (clone $query)->where('status', 'resolved')->count();
    $totalPending = (clone $query)->where('status', 'pending')->count();

    // Calculate success rates
    $verificationRate = $totalSubmissions > 0 ? ($totalVerified / $totalSubmissions) * 100 : 0;
    $resolutionRate = $totalVerified > 0 ? ($totalResolved / $totalVerified) * 100 : 0;
    $overallSuccessRate = $totalSubmissions > 0 ? ($totalResolved / $totalSubmissions) * 100 : 0;

    // Get average time to verification (database agnostic)
    $avgVerificationTimeQuery = Item::whereNotNull('verified_at');
    if ($days) {
      $avgVerificationTimeQuery->where('verified_at', '>=', Carbon::now()->subDays($days));
    }

    $verificationItems = $avgVerificationTimeQuery->get(['created_at', 'verified_at']);
    $avgVerificationTime = $verificationItems->isEmpty() ? 0 :
      $verificationItems->avg(function ($item) {
        return Carbon::parse($item->verified_at)->diffInHours(Carbon::parse($item->created_at));
      });

    // Get average time to resolution (database agnostic)
    $avgResolutionTimeQuery = Item::whereNotNull('resolved_at')->whereNotNull('verified_at');
    if ($days) {
      $avgResolutionTimeQuery->where('resolved_at', '>=', Carbon::now()->subDays($days));
    }

    $resolutionItems = $avgResolutionTimeQuery->get(['verified_at', 'resolved_at']);
    $avgResolutionTime = $resolutionItems->isEmpty() ? 0 :
      $resolutionItems->avg(function ($item) {
        return Carbon::parse($item->resolved_at)->diffInDays(Carbon::parse($item->verified_at));
      });

    return [
      'verification_rate' => round($verificationRate, 2),
      'resolution_rate' => round($resolutionRate, 2),
      'overall_success_rate' => round($overallSuccessRate, 2),
      'avg_verification_time_hours' => round($avgVerificationTime ?? 0, 2),
      'avg_resolution_time_days' => round($avgResolutionTime ?? 0, 2),
      'total_verified' => $totalVerified,
      'total_resolved' => $totalResolved,
      'total_submissions' => $totalSubmissions,
      'total_pending' => $totalPending,
    ];
  }

  /**
   * Get monthly statistics for the current year.
   */
  public function getMonthlyStats(): Collection
  {
    $currentYear = Carbon::now()->year;

    return Item::select(
      DB::raw('strftime("%m", created_at) as month'),
      DB::raw('strftime("%m", created_at) as month_name'),
      DB::raw('COUNT(*) as total_items'),
      DB::raw('SUM(CASE WHEN type = "lost" THEN 1 ELSE 0 END) as lost_items'),
      DB::raw('SUM(CASE WHEN type = "found" THEN 1 ELSE 0 END) as found_items'),
      DB::raw('SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) as verified_items'),
      DB::raw('SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved_items')
    )
      ->whereYear('created_at', $currentYear)
      ->groupBy(DB::raw('strftime("%m", created_at)'))
      ->orderBy('month')
      ->get();
  }

  /**
   * Get top categories by item count.
   */
  public function getTopCategories(int $limit = 10, int $days = null): Collection
  {
    $query = Category::withCount(['items' => function ($query) use ($days) {
      if ($days) {
        $query->where('created_at', '>=', Carbon::now()->subDays($days));
      }
    }]);

    return $query->orderBy('items_count', 'desc')
      ->limit($limit)
      ->get();
  }

  /**
   * Get recent activity statistics.
   */
  public function getRecentActivity(int $days = 7): array
  {
    $startDate = Carbon::now()->subDays($days);

    return [
      'recent_submissions' => Item::where('created_at', '>=', $startDate)->count(),
      'recent_verifications' => Item::where('verified_at', '>=', $startDate)->count(),
      'recent_resolutions' => Item::where('resolved_at', '>=', $startDate)->count(),
      'recent_lost_items' => Item::where('created_at', '>=', $startDate)
        ->where('type', 'lost')->count(),
      'recent_found_items' => Item::where('created_at', '>=', $startDate)
        ->where('type', 'found')->count(),
    ];
  }

  /**
   * Get location-based statistics.
   */
  public function getLocationStats(): Collection
  {
    return Item::select(
      'location',
      DB::raw('COUNT(*) as total_items'),
      DB::raw('SUM(CASE WHEN type = "lost" THEN 1 ELSE 0 END) as lost_items'),
      DB::raw('SUM(CASE WHEN type = "found" THEN 1 ELSE 0 END) as found_items'),
      DB::raw('SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved_items')
    )
      ->groupBy('location')
      ->orderBy('total_items', 'desc')
      ->limit(20)
      ->get();
  }

  /**
   * Export statistical data for reports.
   */
  public function exportStatisticalData(array $options = []): array
  {
    $data = [
      'generated_at' => Carbon::now()->toISOString(),
      'overview' => $this->getOverviewStats(),
      'success_metrics' => $this->getSuccessRateMetrics(),
    ];

    if ($options['include_categories'] ?? true) {
      $data['categories'] = $this->getItemCountsByCategory();
    }

    if ($options['include_trends'] ?? true) {
      $trendDays = $options['trend_days'] ?? 30;
      $data['submission_trends'] = $this->getSubmissionTrends($trendDays);
      $data['verification_trends'] = $this->getVerificationTrends($trendDays);
    }

    if ($options['include_monthly'] ?? true) {
      $data['monthly_stats'] = $this->getMonthlyStats();
    }

    if ($options['include_locations'] ?? false) {
      $data['location_stats'] = $this->getLocationStats();
    }

    if ($options['include_recent'] ?? true) {
      $recentDays = $options['recent_days'] ?? 7;
      $data['recent_activity'] = $this->getRecentActivity($recentDays);
    }

    return $data;
  }

  /**
   * Get performance metrics for admin dashboard.
   */
  public function getPerformanceMetrics(): array
  {
    // Calculate pending queue metrics
    $pendingItems = Item::pending()->get();
    $avgPendingTime = $pendingItems->isEmpty() ? 0 :
      $pendingItems->avg(function ($item) {
        return Carbon::parse($item->created_at)->diffInHours(Carbon::now());
      });

    // Get verification performance
    $verifiedToday = Item::whereDate('verified_at', Carbon::today())->count();
    $verifiedThisWeek = Item::where('verified_at', '>=', Carbon::now()->startOfWeek())->count();

    // Get user engagement metrics
    $activeUsers = User::whereHas('items', function ($query) {
      $query->where('created_at', '>=', Carbon::now()->subDays(30));
    })->count();

    return [
      'pending_queue_size' => $pendingItems->count(),
      'avg_pending_time_hours' => round($avgPendingTime, 2),
      'verifications_today' => $verifiedToday,
      'verifications_this_week' => $verifiedThisWeek,
      'active_users_30_days' => $activeUsers,
      'items_needing_attention' => Item::where('created_at', '<', Carbon::now()->subDays(7))
        ->where('status', 'pending')
        ->count(),
    ];
  }

  /**
   * Generate comparison statistics between time periods.
   */
  public function getComparisonStats(int $currentPeriodDays = 30, int $previousPeriodDays = 30): array
  {
    $currentStart = Carbon::now()->subDays($currentPeriodDays);
    $previousStart = Carbon::now()->subDays($currentPeriodDays + $previousPeriodDays);
    $previousEnd = Carbon::now()->subDays($currentPeriodDays);

    $currentStats = [
      'submissions' => Item::where('created_at', '>=', $currentStart)->count(),
      'verifications' => Item::where('verified_at', '>=', $currentStart)->count(),
      'resolutions' => Item::where('resolved_at', '>=', $currentStart)->count(),
    ];

    $previousStats = [
      'submissions' => Item::whereBetween('created_at', [$previousStart, $previousEnd])->count(),
      'verifications' => Item::whereBetween('verified_at', [$previousStart, $previousEnd])->count(),
      'resolutions' => Item::whereBetween('resolved_at', [$previousStart, $previousEnd])->count(),
    ];

    // Calculate percentage changes
    $changes = [];
    foreach ($currentStats as $key => $current) {
      $previous = $previousStats[$key];
      if ($previous > 0) {
        $changes[$key] = round((($current - $previous) / $previous) * 100, 2);
      } else {
        $changes[$key] = $current > 0 ? 100 : 0;
      }
    }

    return [
      'current_period' => $currentStats,
      'previous_period' => $previousStats,
      'percentage_changes' => $changes,
    ];
  }

  /**
   * Get submission count for a specific number of days.
   */
  public function getSubmissionCount(int $days = 7): int
  {
    return Item::where('created_at', '>=', Carbon::now()->subDays($days))->count();
  }

  /**
   * Get verified items count for a specific number of days.
   */
  public function getVerifiedCount(int $days = 7): int
  {
    return Item::where('verified_at', '>=', Carbon::now()->subDays($days))->count();
  }

  /**
   * Get rejected items count for a specific number of days.
   */
  public function getRejectedCount(int $days = 7): int
  {
    return Item::where('status', 'rejected')
      ->where('updated_at', '>=', Carbon::now()->subDays($days))
      ->count();
  }

  /**
   * Get resolved items count for a specific number of days.
   */
  public function getResolvedCount(int $days = 7): int
  {
    return Item::where('resolved_at', '>=', Carbon::now()->subDays($days))->count();
  }

  /**
   * Get average verification time in hours for a specific number of days.
   */
  public function getAverageVerificationTime(int $days = 7): float
  {
    $items = Item::whereNotNull('verified_at')
      ->where('verified_at', '>=', Carbon::now()->subDays($days))
      ->get(['created_at', 'verified_at']);

    if ($items->isEmpty()) {
      return 0;
    }

    $avgTime = $items->avg(function ($item) {
      return Carbon::parse($item->verified_at)->diffInHours(Carbon::parse($item->created_at));
    });

    return round($avgTime, 2);
  }



  /**
   * Get user activity statistics for a specific number of days.
   */
  public function getUserActivityStats(int $days = 7): array
  {
    $startDate = Carbon::now()->subDays($days);

    return [
      'new_users' => User::where('created_at', '>=', $startDate)->count(),
      'active_users' => User::whereHas('items', function ($query) use ($startDate) {
        $query->where('created_at', '>=', $startDate);
      })->count(),
      'total_users' => User::count(),
    ];
  }
}
