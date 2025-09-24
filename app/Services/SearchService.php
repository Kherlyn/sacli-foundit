<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Category;
use App\Repositories\ItemRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
  public function __construct(
    private ItemRepository $itemRepository
  ) {}

  /**
   * Perform advanced search with multiple filters and ranking.
   */
  public function advancedSearch(array $filters = [], int $perPage = 15): LengthAwarePaginator
  {
    $query = $filters['query'] ?? '';
    $type = $filters['type'] ?? null;
    $categoryId = $filters['category_id'] ?? null;
    $location = $filters['location'] ?? null;
    $startDate = $filters['start_date'] ?? null;
    $endDate = $filters['end_date'] ?? null;

    return $this->itemRepository->searchItems(
      query: $query,
      type: $type,
      categoryId: $categoryId,
      location: $location,
      startDate: $startDate,
      endDate: $endDate,
      perPage: $perPage
    );
  }

  /**
   * Search with relevance scoring and ranking.
   */
  public function searchWithRelevance(string $query, array $filters = [], int $perPage = 15): LengthAwarePaginator
  {
    $results = $this->advancedSearch(array_merge($filters, ['query' => $query]), $perPage);

    // Apply relevance scoring to the results
    $scoredItems = $results->getCollection()->map(function ($item) use ($query) {
      $item->relevance_score = $this->calculateRelevanceScore($item, $query);
      return $item;
    });

    // Sort by relevance score (highest first)
    $sortedItems = $scoredItems->sortByDesc('relevance_score');

    // Create new paginator with sorted results
    return new LengthAwarePaginator(
      $sortedItems->values(),
      $results->total(),
      $results->perPage(),
      $results->currentPage(),
      ['path' => request()->url(), 'pageName' => 'page']
    );
  }

  /**
   * Calculate relevance score for search results.
   */
  public function calculateRelevanceScore(Item $item, string $query): float
  {
    $score = 0;
    $queryLower = strtolower($query);
    $titleLower = strtolower($item->title);
    $descriptionLower = strtolower($item->description);

    // Exact title match gets highest score
    if ($titleLower === $queryLower) {
      $score += 100;
    }
    // Title starts with query
    elseif (str_starts_with($titleLower, $queryLower)) {
      $score += 80;
    }
    // Title contains query
    elseif (str_contains($titleLower, $queryLower)) {
      $score += 60;
    }

    // Description matches
    if (str_contains($descriptionLower, $queryLower)) {
      $score += 30;
    }

    // Boost score for recent items
    $daysSinceCreated = abs(now()->diffInDays($item->created_at));
    if ($daysSinceCreated <= 7) {
      $score += 20;
    } elseif ($daysSinceCreated <= 30) {
      $score += 10;
    }

    // Boost score for items with images
    if ($item->images && $item->images->count() > 0) {
      $score += 15;
    }

    return $score;
  }

  /**
   * Get search suggestions based on partial query.
   */
  public function getSearchSuggestions(string $partialQuery, int $limit = 10): Collection
  {
    if (strlen($partialQuery) < 2) {
      return collect();
    }

    // Get suggestions from item titles and descriptions
    $items = Item::verified()
      ->where(function ($query) use ($partialQuery) {
        $query->where('title', 'LIKE', "%{$partialQuery}%")
          ->orWhere('description', 'LIKE', "%{$partialQuery}%");
      })
      ->select('title', 'description')
      ->limit($limit * 2) // Get more to filter duplicates
      ->get();

    $suggestions = collect();

    // Extract unique words/phrases from titles
    foreach ($items as $item) {
      $words = explode(' ', $item->title);
      foreach ($words as $word) {
        $word = trim(strtolower($word));
        if (strlen($word) >= 3 && str_contains($word, strtolower($partialQuery))) {
          $suggestions->push($word);
        }
      }
    }

    return $suggestions->unique()->take($limit);
  }

  /**
   * Get popular search terms based on successful matches.
   */
  public function getPopularSearchTerms(int $limit = 10): Collection
  {
    // This would typically be stored in a separate search_logs table
    // For now, we'll extract common words from verified items
    $items = Item::verified()
      ->select('title', 'description')
      ->get();

    $wordCounts = [];

    foreach ($items as $item) {
      $words = str_word_count(strtolower($item->title . ' ' . $item->description), 1);
      foreach ($words as $word) {
        if (strlen($word) >= 3) {
          $wordCounts[$word] = ($wordCounts[$word] ?? 0) + 1;
        }
      }
    }

    arsort($wordCounts);

    return collect($wordCounts)
      ->take($limit)
      ->keys();
  }

  /**
   * Search within a specific category.
   */
  public function searchInCategory(int $categoryId, string $query = '', array $filters = [], int $perPage = 15): LengthAwarePaginator
  {
    return $this->advancedSearch(array_merge($filters, [
      'category_id' => $categoryId,
      'query' => $query,
    ]), $perPage);
  }

  /**
   * Search by location with fuzzy matching.
   */
  public function searchByLocation(string $location, array $filters = [], int $perPage = 15): LengthAwarePaginator
  {
    return $this->advancedSearch(array_merge($filters, [
      'location' => $location,
    ]), $perPage);
  }

  /**
   * Get items similar to a given item.
   */
  public function findSimilarItems(Item $item, int $limit = 5): Collection
  {
    return $this->itemRepository->getSimilarItems($item, $limit);
  }

  /**
   * Full-text search across multiple fields.
   */
  public function fullTextSearch(string $query, array $filters = [], int $perPage = 15): LengthAwarePaginator
  {
    // For databases that support full-text search, this would use those features
    // For now, we'll use the standard search with LIKE queries
    return $this->advancedSearch(array_merge($filters, ['query' => $query]), $perPage);
  }

  /**
   * Search with date range filtering.
   */
  public function searchByDateRange(string $startDate, string $endDate, array $filters = [], int $perPage = 15): LengthAwarePaginator
  {
    return $this->advancedSearch(array_merge($filters, [
      'start_date' => $startDate,
      'end_date' => $endDate,
    ]), $perPage);
  }

  /**
   * Get search statistics and analytics.
   */
  public function getSearchStatistics(): array
  {
    $totalItems = Item::verified()->count();
    $totalCategories = Category::count();
    $itemsByType = Item::verified()
      ->selectRaw('type, COUNT(*) as count')
      ->groupBy('type')
      ->pluck('count', 'type')
      ->toArray();

    $itemsByCategory = Item::verified()
      ->join('categories', 'items.category_id', '=', 'categories.id')
      ->selectRaw('categories.name, COUNT(*) as count')
      ->groupBy('categories.name')
      ->pluck('count', 'name')
      ->toArray();

    return [
      'total_searchable_items' => $totalItems,
      'total_categories' => $totalCategories,
      'items_by_type' => $itemsByType,
      'items_by_category' => $itemsByCategory,
      'avg_items_per_category' => $totalCategories > 0 ? round($totalItems / $totalCategories, 2) : 0,
    ];
  }

  /**
   * Validate search filters.
   */
  public function validateSearchFilters(array $filters): array
  {
    $validatedFilters = [];

    // Validate query
    if (isset($filters['query'])) {
      $validatedFilters['query'] = trim($filters['query']);
      if (strlen($validatedFilters['query']) > 255) {
        $validatedFilters['query'] = substr($validatedFilters['query'], 0, 255);
      }
    }

    // Validate type
    if (isset($filters['type']) && in_array($filters['type'], ['lost', 'found'])) {
      $validatedFilters['type'] = $filters['type'];
    }

    // Validate category_id
    if (isset($filters['category_id']) && is_numeric($filters['category_id'])) {
      $categoryExists = Category::where('id', $filters['category_id'])->exists();
      if ($categoryExists) {
        $validatedFilters['category_id'] = (int) $filters['category_id'];
      }
    }

    // Validate location
    if (isset($filters['location'])) {
      $validatedFilters['location'] = trim($filters['location']);
      if (strlen($validatedFilters['location']) > 255) {
        $validatedFilters['location'] = substr($validatedFilters['location'], 0, 255);
      }
    }

    // Validate dates
    if (isset($filters['start_date'])) {
      try {
        $startDate = \Carbon\Carbon::parse($filters['start_date']);
        $validatedFilters['start_date'] = $startDate->format('Y-m-d');
      } catch (\Exception $e) {
        // Invalid date, skip
      }
    }

    if (isset($filters['end_date'])) {
      try {
        $endDate = \Carbon\Carbon::parse($filters['end_date']);
        $validatedFilters['end_date'] = $endDate->format('Y-m-d');
      } catch (\Exception $e) {
        // Invalid date, skip
      }
    }

    // Ensure start_date is before end_date
    if (isset($validatedFilters['start_date']) && isset($validatedFilters['end_date'])) {
      if ($validatedFilters['start_date'] > $validatedFilters['end_date']) {
        unset($validatedFilters['end_date']);
      }
    }

    return $validatedFilters;
  }

  /**
   * Get recent searches (would typically come from a search_logs table).
   */
  public function getRecentSearches(int $limit = 10): Collection
  {
    // This is a placeholder implementation
    // In a real application, you would store search queries in a database
    return collect([
      'iPhone',
      'wallet',
      'keys',
      'laptop',
      'backpack',
      'glasses',
      'phone',
      'documents',
      'jewelry',
      'camera'
    ])->take($limit);
  }

  /**
   * Clear search cache (if caching is implemented).
   */
  public function clearSearchCache(): bool
  {
    // Placeholder for cache clearing logic
    // In a real application, you might use Redis or other caching mechanisms
    return true;
  }
}
