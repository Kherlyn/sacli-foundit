<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use App\Repositories\ItemRepository;
use App\Services\SearchService;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->category = Category::factory()->create(['name' => 'Electronics']);
  $this->repository = new ItemRepository();
  $this->service = new SearchService($this->repository);

  // Create verified items for searching
  Item::factory()->verified()->create([
    'title' => 'Lost iPhone 12 Pro',
    'description' => 'Black iPhone 12 Pro lost in Central Park yesterday',
    'type' => 'lost',
    'category_id' => $this->category->id,
    'user_id' => $this->user->id,
    'location' => 'Central Park, NYC',
    'date_occurred' => now()->subDays(2),
  ]);

  Item::factory()->verified()->create([
    'title' => 'Found Wallet',
    'description' => 'Brown leather wallet found near subway station',
    'type' => 'found',
    'category_id' => $this->category->id,
    'user_id' => $this->user->id,
    'location' => 'Subway Station',
    'date_occurred' => now()->subDays(1),
  ]);

  Item::factory()->verified()->create([
    'title' => 'Lost Car Keys',
    'description' => 'Toyota car keys with blue keychain lost in parking lot',
    'type' => 'lost',
    'category_id' => $this->category->id,
    'user_id' => $this->user->id,
    'location' => 'Mall Parking Lot',
    'date_occurred' => now()->subDays(3),
  ]);

  // Create pending item (should not appear in searches)
  Item::factory()->pending()->create([
    'title' => 'Pending Item',
    'description' => 'This should not appear in public searches',
    'type' => 'lost',
    'category_id' => $this->category->id,
    'user_id' => $this->user->id,
  ]);
});

describe('SearchService Advanced Search', function () {
  it('performs basic search by query', function () {
    $result = $this->service->advancedSearch(['query' => 'iPhone']);

    expect($result->total())->toBe(1);
    expect($result->items()[0]->title)->toContain('iPhone');
  });

  it('searches by item type', function () {
    $lostResult = $this->service->advancedSearch(['type' => 'lost']);
    $foundResult = $this->service->advancedSearch(['type' => 'found']);

    expect($lostResult->total())->toBe(2);
    expect($foundResult->total())->toBe(1);

    foreach ($lostResult->items() as $item) {
      expect($item->type)->toBe('lost');
    }

    foreach ($foundResult->items() as $item) {
      expect($item->type)->toBe('found');
    }
  });

  it('searches by category', function () {
    $result = $this->service->advancedSearch(['category_id' => $this->category->id]);

    expect($result->total())->toBe(3);

    foreach ($result->items() as $item) {
      expect($item->category_id)->toBe($this->category->id);
    }
  });

  it('searches by location', function () {
    $result = $this->service->advancedSearch(['location' => 'Park']);

    expect($result->total())->toBeGreaterThanOrEqual(1);

    foreach ($result->items() as $item) {
      expect($item->location)->toContain('Park');
    }
  });

  it('searches by date range', function () {
    $result = $this->service->advancedSearch([
      'start_date' => now()->subDays(2)->format('Y-m-d'),
      'end_date' => now()->format('Y-m-d'),
    ]);

    expect($result->total())->toBe(2); // iPhone and Wallet

    foreach ($result->items() as $item) {
      expect($item->date_occurred)->toBeGreaterThanOrEqual(now()->subDays(2)->startOfDay());
    }
  });

  it('combines multiple filters', function () {
    $result = $this->service->advancedSearch([
      'query' => 'lost',
      'type' => 'lost',
      'category_id' => $this->category->id,
    ]);

    expect($result->total())->toBe(2);

    foreach ($result->items() as $item) {
      expect($item->type)->toBe('lost');
      expect($item->category_id)->toBe($this->category->id);
    }
  });

  it('returns empty results for non-matching query', function () {
    $result = $this->service->advancedSearch(['query' => 'nonexistent']);

    expect($result->total())->toBe(0);
  });

  it('only returns verified items', function () {
    $result = $this->service->advancedSearch();

    expect($result->total())->toBe(3); // Only verified items, not pending

    foreach ($result->items() as $item) {
      expect($item->status)->toBe('verified');
    }
  });
});

describe('SearchService Relevance Scoring', function () {
  it('calculates relevance score for exact title match', function () {
    $item = Item::factory()->make([
      'title' => 'iPhone',
      'description' => 'A smartphone',
      'created_at' => now()->subDays(1),
    ]);

    $score = $this->service->calculateRelevanceScore($item, 'iPhone');

    expect($score)->toBeGreaterThan(100); // Exact match + recent bonus
  });

  it('calculates relevance score for title starts with query', function () {
    $item = Item::factory()->make([
      'title' => 'iPhone 12 Pro',
      'description' => 'A smartphone device',
      'created_at' => now()->subDays(40), // Old enough to not get recent bonus
    ]);

    // Ensure no images relation
    $item->setRelation('images', collect());

    $score = $this->service->calculateRelevanceScore($item, 'iPhone');

    expect($score)->toBeGreaterThanOrEqual(80); // Title starts with, may have other bonuses
  });

  it('calculates relevance score for title contains query', function () {
    $item = Item::factory()->make([
      'title' => 'Lost iPhone 12',
      'description' => 'A smartphone device',
      'created_at' => now()->subDays(40), // Old enough to not get recent bonus
    ]);

    $score = $this->service->calculateRelevanceScore($item, 'iPhone');

    expect($score)->toBeGreaterThanOrEqual(60); // Title contains, may have other bonuses
  });

  it('adds bonus for recent items', function () {
    $recentItem = Item::factory()->make([
      'title' => 'Test Item',
      'description' => 'iPhone mentioned here',
      'created_at' => now()->subDays(3), // Should get 20 point bonus
    ]);
    $recentItem->setRelation('images', collect());

    $oldItem = Item::factory()->make([
      'title' => 'Test Item',
      'description' => 'iPhone mentioned here',
      'created_at' => now()->subDays(60), // Should get no bonus
    ]);
    $oldItem->setRelation('images', collect());

    $recentScore = $this->service->calculateRelevanceScore($recentItem, 'iPhone');
    $oldScore = $this->service->calculateRelevanceScore($oldItem, 'iPhone');

    expect($recentScore)->toBeGreaterThan($oldScore);
    expect($recentScore - $oldScore)->toBe(20.0); // Should be exactly 20 points difference
  });

  it('adds bonus for items with images', function () {
    $itemWithImages = Item::factory()->make([
      'title' => 'Test Item',
      'description' => 'iPhone mentioned here',
      'created_at' => now()->subDays(10),
    ]);
    $itemWithImages->setRelation('images', collect([
      ItemImage::factory()->make(),
      ItemImage::factory()->make(),
    ]));

    $itemWithoutImages = Item::factory()->make([
      'title' => 'Test Item',
      'description' => 'iPhone mentioned here',
      'created_at' => now()->subDays(10),
    ]);
    $itemWithoutImages->setRelation('images', collect());

    $scoreWithImages = $this->service->calculateRelevanceScore($itemWithImages, 'iPhone');
    $scoreWithoutImages = $this->service->calculateRelevanceScore($itemWithoutImages, 'iPhone');

    expect($scoreWithImages)->toBeGreaterThan($scoreWithoutImages);
  });

  it('performs search with relevance ranking', function () {
    // Create items with different relevance scores
    Item::factory()->verified()->create([
      'title' => 'iPhone', // Exact match - highest score
      'description' => 'Lost iPhone',
      'category_id' => $this->category->id,
      'user_id' => $this->user->id,
      'created_at' => now()->subDays(1),
    ]);

    Item::factory()->verified()->create([
      'title' => 'iPhone 12 Pro', // Starts with - medium score
      'description' => 'Lost smartphone',
      'category_id' => $this->category->id,
      'user_id' => $this->user->id,
      'created_at' => now()->subDays(10),
    ]);

    $result = $this->service->searchWithRelevance('iPhone');

    expect($result->total())->toBeGreaterThanOrEqual(2);

    $items = $result->items();
    expect($items[0]->title)->toBe('iPhone'); // Highest relevance first
  });
});

describe('SearchService Search Suggestions', function () {
  it('gets search suggestions for partial query', function () {
    $suggestions = $this->service->getSearchSuggestions('iph');

    expect($suggestions)->not->toBeEmpty();
    expect($suggestions->contains('iphone'))->toBeTrue();
  });

  it('returns empty suggestions for very short query', function () {
    $suggestions = $this->service->getSearchSuggestions('i');

    expect($suggestions)->toBeEmpty();
  });

  it('limits number of suggestions', function () {
    $suggestions = $this->service->getSearchSuggestions('l', 3);

    expect($suggestions->count())->toBeLessThanOrEqual(3);
  });

  it('gets popular search terms', function () {
    $popularTerms = $this->service->getPopularSearchTerms(5);

    expect($popularTerms)->not->toBeEmpty();
    expect($popularTerms->count())->toBeLessThanOrEqual(5);
  });
});

describe('SearchService Specialized Searches', function () {
  it('searches within specific category', function () {
    $result = $this->service->searchInCategory($this->category->id, 'iPhone');

    expect($result->total())->toBe(1);
    expect($result->items()[0]->category_id)->toBe($this->category->id);
    expect($result->items()[0]->title)->toContain('iPhone');
  });

  it('searches by location with fuzzy matching', function () {
    $result = $this->service->searchByLocation('Park');

    expect($result->total())->toBeGreaterThanOrEqual(1);

    foreach ($result->items() as $item) {
      expect($item->location)->toContain('Park');
    }
  });

  it('finds similar items', function () {
    $mainItem = Item::factory()->verified()->create([
      'category_id' => $this->category->id,
      'type' => 'lost',
      'user_id' => $this->user->id,
    ]);

    $similar = $this->service->findSimilarItems($mainItem);

    expect($similar)->not->toBeEmpty();

    foreach ($similar as $item) {
      expect($item->category_id)->toBe($this->category->id);
      expect($item->type)->toBe('lost');
      expect($item->id)->not->toBe($mainItem->id);
    }
  });

  it('performs full-text search', function () {
    $result = $this->service->fullTextSearch('iPhone');

    expect($result->total())->toBeGreaterThanOrEqual(1);
  });

  it('searches by date range', function () {
    $startDate = now()->subDays(3)->format('Y-m-d');
    $endDate = now()->subDays(1)->format('Y-m-d');

    $result = $this->service->searchByDateRange($startDate, $endDate);

    expect($result->total())->toBeGreaterThanOrEqual(1);

    foreach ($result->items() as $item) {
      expect($item->date_occurred)->toBeGreaterThanOrEqual($startDate);
      expect($item->date_occurred)->toBeLessThanOrEqual($endDate);
    }
  });
});

describe('SearchService Statistics and Analytics', function () {
  it('gets search statistics', function () {
    $stats = $this->service->getSearchStatistics();

    expect($stats)->toHaveKey('total_searchable_items');
    expect($stats)->toHaveKey('total_categories');
    expect($stats)->toHaveKey('items_by_type');
    expect($stats)->toHaveKey('items_by_category');
    expect($stats)->toHaveKey('avg_items_per_category');

    expect($stats['total_searchable_items'])->toBe(3);
    expect($stats['total_categories'])->toBe(1);
    expect($stats['items_by_type'])->toHaveKey('lost');
    expect($stats['items_by_type'])->toHaveKey('found');
  });

  it('calculates average items per category', function () {
    $stats = $this->service->getSearchStatistics();

    expect($stats['avg_items_per_category'])->toBe(3.0);
  });
});

describe('SearchService Filter Validation', function () {
  it('validates and sanitizes search query', function () {
    $filters = ['query' => '  iPhone 12  '];
    $validated = $this->service->validateSearchFilters($filters);

    expect($validated['query'])->toBe('iPhone 12');
  });

  it('truncates long search query', function () {
    $longQuery = str_repeat('a', 300);
    $filters = ['query' => $longQuery];
    $validated = $this->service->validateSearchFilters($filters);

    expect(strlen($validated['query']))->toBe(255);
  });

  it('validates item type', function () {
    $validFilters = ['type' => 'lost'];
    $invalidFilters = ['type' => 'invalid'];

    $validatedValid = $this->service->validateSearchFilters($validFilters);
    $validatedInvalid = $this->service->validateSearchFilters($invalidFilters);

    expect($validatedValid)->toHaveKey('type');
    expect($validatedValid['type'])->toBe('lost');
    expect($validatedInvalid)->not->toHaveKey('type');
  });

  it('validates category existence', function () {
    $validFilters = ['category_id' => $this->category->id];
    $invalidFilters = ['category_id' => 999];

    $validatedValid = $this->service->validateSearchFilters($validFilters);
    $validatedInvalid = $this->service->validateSearchFilters($invalidFilters);

    expect($validatedValid)->toHaveKey('category_id');
    expect($validatedValid['category_id'])->toBe($this->category->id);
    expect($validatedInvalid)->not->toHaveKey('category_id');
  });

  it('validates and formats dates', function () {
    $filters = [
      'start_date' => '2023-12-01',
      'end_date' => '2023-12-31',
    ];

    $validated = $this->service->validateSearchFilters($filters);

    expect($validated)->toHaveKey('start_date');
    expect($validated)->toHaveKey('end_date');
    expect($validated['start_date'])->toBe('2023-12-01');
    expect($validated['end_date'])->toBe('2023-12-31');
  });

  it('handles invalid dates', function () {
    $filters = [
      'start_date' => 'invalid-date',
      'end_date' => '2023-13-45',
    ];

    $validated = $this->service->validateSearchFilters($filters);

    expect($validated)->not->toHaveKey('start_date');
    expect($validated)->not->toHaveKey('end_date');
  });

  it('ensures start date is before end date', function () {
    $filters = [
      'start_date' => '2023-12-31',
      'end_date' => '2023-12-01',
    ];

    $validated = $this->service->validateSearchFilters($filters);

    expect($validated)->toHaveKey('start_date');
    expect($validated)->not->toHaveKey('end_date');
  });

  it('validates and sanitizes location', function () {
    $filters = ['location' => '  Central Park  '];
    $validated = $this->service->validateSearchFilters($filters);

    expect($validated['location'])->toBe('Central Park');
  });

  it('truncates long location', function () {
    $longLocation = str_repeat('a', 300);
    $filters = ['location' => $longLocation];
    $validated = $this->service->validateSearchFilters($filters);

    expect(strlen($validated['location']))->toBe(255);
  });
});

describe('SearchService Utility Methods', function () {
  it('gets recent searches', function () {
    $recentSearches = $this->service->getRecentSearches(5);

    expect($recentSearches)->not->toBeEmpty();
    expect($recentSearches->count())->toBeLessThanOrEqual(5);
  });

  it('clears search cache', function () {
    $result = $this->service->clearSearchCache();

    expect($result)->toBeTrue();
  });
});
