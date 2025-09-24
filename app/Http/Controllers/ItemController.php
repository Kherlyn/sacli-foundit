<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ItemController extends Controller
{
  public function __construct(
    private ItemService $itemService
  ) {
    // Middleware is applied in routes
  }

  /**
   * Display the item submission form.
   */
  public function create(Request $request): View
  {
    $type = $request->query('type', 'lost'); // Default to 'lost'

    // Validate type parameter
    if (!in_array($type, ['lost', 'found'])) {
      $type = 'lost';
    }

    $categories = Category::orderBy('name')->get();

    return view('items.create', compact('type', 'categories'));
  }

  /**
   * Store a newly created item in storage.
   */
  public function store(Request $request): RedirectResponse
  {
    try {
      // Prepare data for validation and creation
      $data = $request->only([
        'title',
        'description',
        'category_id',
        'type',
        'location',
        'date_occurred',
        'contact_method',
        'contact_email',
        'contact_phone'
      ]);

      // Format contact info as expected by the model
      $data['contact_info'] = [
        'method' => $data['contact_method'],
        'email' => $data['contact_email'] ?? null,
        'phone' => $data['contact_phone'] ?? null,
      ];

      // Add authenticated user ID
      $data['user_id'] = Auth::id();

      // Handle image uploads
      $images = [];
      if ($request->hasFile('images')) {
        $images = $request->file('images');
      }

      // Create the item using the service
      $item = $this->itemService->createItem($data, $images);

      return redirect()
        ->route('items.my-items')
        ->with('success', 'Your item has been submitted successfully! Reference number: #' . $item->id . '. It will be reviewed by our team before being published.');
    } catch (\Illuminate\Validation\ValidationException $e) {
      return redirect()
        ->back()
        ->withErrors($e->validator)
        ->withInput();
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->with('error', 'An error occurred while submitting your item. Please try again.')
        ->withInput();
    }
  }

  /**
   * Display the user's submitted items.
   */
  public function myItems(Request $request): View
  {
    $items = $this->itemService->getUserItems(Auth::id(), 10);

    return view('items.my-items', compact('items'));
  }

  /**
   * Show the form for editing the specified item.
   */
  public function edit(Item $item): View
  {
    // Check if user can edit this item
    if (!$this->itemService->canUserEditItem($item, Auth::user())) {
      abort(403, 'You can only edit your own items that are still pending verification.');
    }

    $categories = Category::orderBy('name')->get();

    return view('items.edit', compact('item', 'categories'));
  }

  /**
   * Update the specified item in storage.
   */
  public function update(Request $request, Item $item): RedirectResponse
  {
    // Check if user can edit this item
    if (!$this->itemService->canUserEditItem($item, Auth::user())) {
      abort(403, 'You can only edit your own items that are still pending verification.');
    }

    try {
      // Prepare data for validation and update
      $data = $request->only([
        'title',
        'description',
        'category_id',
        'type',
        'location',
        'date_occurred',
        'contact_method',
        'contact_email',
        'contact_phone'
      ]);

      // Format contact info as expected by the model
      if ($request->has('contact_method')) {
        $data['contact_info'] = [
          'method' => $data['contact_method'],
          'email' => $data['contact_email'] ?? null,
          'phone' => $data['contact_phone'] ?? null,
        ];
      }

      // Handle new image uploads
      $images = [];
      if ($request->hasFile('images')) {
        $images = $request->file('images');
      }

      // Update the item using the service
      $updatedItem = $this->itemService->updateItem($item, $data, $images);

      return redirect()
        ->route('items.my-items')
        ->with('success', 'Your item has been updated successfully!');
    } catch (\Illuminate\Validation\ValidationException $e) {
      return redirect()
        ->back()
        ->withErrors($e->validator)
        ->withInput();
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->with('error', 'An error occurred while updating your item. Please try again.')
        ->withInput();
    }
  }

  /**
   * Display the specified item (for item owner).
   */
  public function show(Item $item): View
  {
    // Check if user owns this item or if it's public
    if ($item->user_id !== Auth::id() && !$item->isVerified()) {
      abort(403, 'You can only view your own items or publicly verified items.');
    }

    $item->load(['category', 'user', 'images']);

    return view('items.show', compact('item'));
  }

  /**
   * Remove an image from the item.
   */
  public function removeImage(Item $item, int $imageId): RedirectResponse
  {
    // Check if user owns this item
    if ($item->user_id !== Auth::id()) {
      abort(403, 'You can only modify your own items.');
    }

    // Check if item can still be edited
    if (!$this->itemService->canUserEditItem($item, Auth::user())) {
      abort(403, 'You can only edit items that are still pending verification.');
    }

    $success = $this->itemService->removeItemImage($item, $imageId);

    if ($success) {
      return redirect()
        ->back()
        ->with('success', 'Image removed successfully.');
    } else {
      return redirect()
        ->back()
        ->with('error', 'Image not found or could not be removed.');
    }
  }

  /**
   * Delete the specified item.
   */
  public function destroy(Item $item): RedirectResponse
  {
    // Check if user owns this item
    if ($item->user_id !== Auth::id()) {
      abort(403, 'You can only delete your own items.');
    }

    // Only allow deletion of pending items
    if (!$item->isPending()) {
      abort(403, 'You can only delete items that are still pending verification.');
    }

    try {
      $this->itemService->deleteItem($item);

      return redirect()
        ->route('items.my-items')
        ->with('success', 'Item deleted successfully.');
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->with('error', 'An error occurred while deleting the item. Please try again.');
    }
  }
}
