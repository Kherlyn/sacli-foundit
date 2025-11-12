<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\ItemRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
  public function __construct(
    private ItemRepository $itemRepository
  ) {}

  /**
   * Display the landing page with search interface.
   */
  public function index(): View
  {
    $categories = Category::orderBy('name')->get();
    $recentItems = $this->itemRepository->getRecentItems(6);
    $statistics = $this->itemRepository->getStatistics();

    return view('public.index', compact('categories', 'recentItems', 'statistics'));
  }

  /**
   * Process search queries and return results.
   */
  public function search(Request $request): View
  {
    $request->validate([
      'query' => 'nullable|string|max:255',
      'type' => 'nullable|in:lost,found',
      'category_id' => 'nullable|exists:categories,id',
      'location' => 'nullable|string|max:255',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date|after_or_equal:start_date',
      'sort' => 'nullable|in:newest,oldest,relevance',
    ]);

    $query = $request->input('query');
    $type = $request->input('type');
    $categoryId = $request->input('category_id');
    $location = $request->input('location');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $sort = $request->input('sort', 'newest');

    $items = $this->itemRepository->searchItems(
      $query,
      $type,
      $categoryId,
      $location,
      $startDate,
      $endDate,
      $sort,
      12 // items per page
    );

    $categories = Category::orderBy('name')->get();

    return view('public.search', compact(
      'items',
      'categories',
      'query',
      'type',
      'categoryId',
      'location',
      'startDate',
      'endDate'
    ));
  }

  /**
   * Display individual item detail page.
   */
  public function show(int $id): View
  {
    $item = $this->itemRepository->findPublicItem($id);

    if (!$item) {
      abort(404, 'Item not found or not publicly available.');
    }

    // Get other recent items (not similar, just other items)
    $otherItems = $this->itemRepository->getRecentPublicItems(4, [$id]);

    return view('public.show', compact('item', 'otherItems'));
  }

  /**
   * Category-based item browsing with filters.
   */
  public function browse(Request $request): View
  {
    $request->validate([
      'category' => 'nullable|exists:categories,id',
      'type' => 'nullable|in:lost,found',
      'location' => 'nullable|string|max:255',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date|after_or_equal:start_date',
      'sort' => 'nullable|in:newest,oldest,relevance,category,location',
    ]);

    $categoryId = $request->input('category');
    $type = $request->input('type');
    $location = $request->input('location');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $sort = $request->input('sort', 'newest');

    $items = $this->itemRepository->searchItems(
      null, // no text query for browsing
      $type,
      $categoryId,
      $location,
      $startDate,
      $endDate,
      $sort,
      12 // items per page
    );

    $categories = Category::orderBy('name')->get();
    $selectedCategory = $categoryId ? Category::find($categoryId) : null;

    return view('public.browse', compact(
      'items',
      'categories',
      'selectedCategory',
      'type',
      'location',
      'startDate',
      'endDate'
    ));
  }
}
