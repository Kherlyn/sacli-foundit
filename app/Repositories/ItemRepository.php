<?php

namespace App\Repositories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ItemRepository
{
  /**
   * Find public items (verified items).
   */
  public function findPublicItems(int $perPage = 15): LengthAwarePaginator
  {
    return Item::public()
      ->with(['category', 'user', 'images'])
      ->orderBy('created_at', 'desc')
      ->paginate($perPage);
  }

  /**
   * Search items with filters.
   */
  public function searchItems(
    ?string $query = null,
    ?string $type = null,
    ?int $categoryId = null,
    ?string $location = null,
    ?string $startDate = null,
    ?string $endDate = null,
    string $sort = 'newest',
    int $perPage = 15
  ): LengthAwarePaginator {
    $builder = Item::public()
      ->with(['category', 'user', 'images']);

    if ($query) {
      $builder->search($query);
    }

    if ($type) {
      $builder->ofType($type);
    }

    if ($categoryId) {
      $builder->inCategory($categoryId);
    }

    if ($location) {
      $builder->nearLocation($location);
    }

    if ($startDate || $endDate) {
      $builder->dateRange($startDate, $endDate);
    }

    // Apply sorting
    switch ($sort) {
      case 'oldest':
        $builder->orderBy('created_at', 'asc');
        break;
      case 'relevance':
        // If there's a search query, order by relevance (search score)
        // Otherwise fall back to newest
        if ($query) {
          $builder->orderByRaw('CASE WHEN name LIKE ? THEN 1 WHEN description LIKE ? THEN 2 ELSE 3 END', ["%{$query}%", "%{$query}%"])
            ->orderBy('created_at', 'desc');
        } else {
          $builder->orderBy('created_at', 'desc');
        }
        break;
      case 'category':
        $builder->join('categories', 'items.category_id', '=', 'categories.id')
          ->orderBy('categories.name', 'asc')
          ->orderBy('items.created_at', 'desc')
          ->select('items.*');
        break;
      case 'location':
        $builder->orderBy('location', 'asc')
          ->orderBy('created_at', 'desc');
        break;
      case 'newest':
      default:
        $builder->orderBy('created_at', 'desc');
        break;
    }

    return $builder->paginate($perPage);
  }

  /**
   * Get pending items for admin verification.
   */
  public function getPendingItems(int $perPage = 15): LengthAwarePaginator
  {
    return Item::pending()
      ->with(['category', 'user', 'images'])
      ->orderBy('created_at', 'asc')
      ->paginate($perPage);
  }

  /**
   * Get items by user.
   */
  public function getItemsByUser(int $userId, int $perPage = 15): LengthAwarePaginator
  {
    return Item::where('user_id', $userId)
      ->with(['category', 'images'])
      ->orderBy('created_at', 'desc')
      ->paginate($perPage);
  }

  /**
   * Get items by status.
   */
  public function getItemsByStatus(string $status, int $perPage = 15): LengthAwarePaginator
  {
    return Item::where('status', $status)
      ->with(['category', 'user', 'images'])
      ->orderBy('created_at', 'desc')
      ->paginate($perPage);
  }

  /**
   * Get recent items.
   */
  public function getRecentItems(int $limit = 10): Collection
  {
    return Item::public()
      ->with(['category', 'user', 'images'])
      ->orderBy('created_at', 'desc')
      ->limit($limit)
      ->get();
  }

  /**
   * Get items by category.
   */
  public function getItemsByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
  {
    return Item::public()
      ->inCategory($categoryId)
      ->with(['category', 'user', 'images'])
      ->orderBy('created_at', 'desc')
      ->paginate($perPage);
  }

  /**
   * Get statistics for dashboard.
   */
  public function getStatistics(): array
  {
    return [
      'total_items' => Item::count(),
      'pending_items' => Item::pending()->count(),
      'verified_items' => Item::verified()->count(),
      'resolved_items' => Item::where('status', 'resolved')->count(),
      'rejected_items' => Item::where('status', 'rejected')->count(),
      'lost_items' => Item::public()->ofType('lost')->count(),
      'found_items' => Item::public()->ofType('found')->count(),
      'items_this_month' => Item::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count(),
      'items_this_week' => Item::whereBetween('created_at', [
        now()->startOfWeek(),
        now()->endOfWeek()
      ])->count(),
    ];
  }

  /**
   * Get category statistics.
   */
  public function getCategoryStatistics(): Collection
  {
    return Item::public()
      ->selectRaw('category_id, categories.name as category_name, COUNT(*) as count')
      ->join('categories', 'items.category_id', '=', 'categories.id')
      ->groupBy('category_id', 'categories.name')
      ->orderBy('count', 'desc')
      ->get();
  }

  /**
   * Get items trend data for charts.
   */
  public function getItemsTrend(int $days = 30): Collection
  {
    return Item::selectRaw('DATE(created_at) as date, COUNT(*) as count')
      ->where('created_at', '>=', now()->subDays($days))
      ->groupBy('date')
      ->orderBy('date')
      ->get();
  }

  /**
   * Get items by type and status for statistics.
   */
  public function getItemsByTypeAndStatus(): Collection
  {
    return Item::selectRaw('type, status, COUNT(*) as count')
      ->groupBy('type', 'status')
      ->get();
  }

  /**
   * Find item by ID with relationships.
   */
  public function findWithRelations(int $id): ?Item
  {
    return Item::with(['category', 'user', 'images'])
      ->find($id);
  }

  /**
   * Find public item by ID.
   */
  public function findPublicItem(int $id): ?Item
  {
    return Item::public()
      ->with(['category', 'user', 'images'])
      ->find($id);
  }

  /**
   * Get similar items based on category and type.
   */
  public function getSimilarItems(Item $item, int $limit = 5): Collection
  {
    return Item::public()
      ->where('id', '!=', $item->id)
      ->where('category_id', $item->category_id)
      ->where('type', $item->type)
      ->with(['category', 'user', 'images'])
      ->orderBy('created_at', 'desc')
      ->limit($limit)
      ->get();
  }

  /**
   * Get recent public items excluding specific IDs.
   */
  public function getRecentPublicItems(int $limit = 4, array $excludeIds = []): Collection
  {
    $query = Item::public()
      ->with(['category', 'user', 'images'])
      ->orderBy('created_at', 'desc')
      ->limit($limit);

    if (!empty($excludeIds)) {
      $query->whereNotIn('id', $excludeIds);
    }

    return $query->get();
  }

  /**
   * Search items for admin with all statuses.
   */
  public function adminSearchItems(
    ?string $query = null,
    ?string $status = null,
    ?string $type = null,
    ?int $categoryId = null,
    ?string $course = null,
    ?int $year = null,
    int $perPage = 15
  ): LengthAwarePaginator {
    $builder = Item::with(['category', 'user', 'images']);

    if ($query) {
      $builder->search($query);
    }

    if ($status) {
      $builder->where('status', $status);
    }

    if ($type) {
      $builder->ofType($type);
    }

    if ($categoryId) {
      $builder->inCategory($categoryId);
    }

    if ($course) {
      $builder->whereHas('user', function ($q) use ($course) {
        $q->where('course', 'like', '%' . $course . '%');
      });
    }

    if ($year) {
      $builder->whereHas('user', function ($q) use ($year) {
        $q->where('year', $year);
      });
    }

    return $builder->orderBy('created_at', 'desc')
      ->paginate($perPage);
  }

  /**
   * Get items requiring attention (pending for too long).
   */
  public function getItemsRequiringAttention(int $daysOld = 7): Collection
  {
    return Item::pending()
      ->where('created_at', '<', now()->subDays($daysOld))
      ->with(['category', 'user'])
      ->orderBy('created_at', 'asc')
      ->get();
  }
}
