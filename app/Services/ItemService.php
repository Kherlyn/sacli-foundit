<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemImage;
use App\Repositories\ItemRepository;
use App\Services\AdminNotificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class ItemService
{
  public function __construct(
    private ItemRepository $itemRepository
  ) {}

  /**
   * Create a new item with validation and image processing.
   */
  public function createItem(array $data, array $images = []): Item
  {
    // Validate the item data
    $this->validateItemData($data);

    // Validate images if provided
    if (!empty($images)) {
      $this->validateImages($images);
    }

    return DB::transaction(function () use ($data, $images) {
      // Create the item
      $item = Item::create([
        'title' => $data['title'],
        'description' => $data['description'],
        'category_id' => $data['category_id'],
        'type' => $data['type'],
        'location' => $data['location'],
        'date_occurred' => $data['date_occurred'],
        'contact_info' => $data['contact_info'],
        'user_id' => $data['user_id'],
        'status' => 'pending',
      ]);

      // Process and store images
      if (!empty($images)) {
        $this->processItemImages($item, $images);
      }

      // Load relationships for notifications
      $item->load(['category', 'user', 'images']);

      // Send admin notification for new submission
      app(AdminNotificationService::class)->notifyNewItemSubmission($item);

      return $item;
    });
  }

  /**
   * Update an existing item.
   */
  public function updateItem(Item $item, array $data, array $images = []): Item
  {
    // Validate only the fields that are being updated
    $this->validateItemDataForUpdate($data);

    // Validate images if provided
    if (!empty($images)) {
      $this->validateImages($images);
    }

    return DB::transaction(function () use ($item, $data, $images) {
      // Update the item
      $item->update([
        'title' => $data['title'] ?? $item->title,
        'description' => $data['description'] ?? $item->description,
        'category_id' => $data['category_id'] ?? $item->category_id,
        'type' => $data['type'] ?? $item->type,
        'location' => $data['location'] ?? $item->location,
        'date_occurred' => $data['date_occurred'] ?? $item->date_occurred,
        'contact_info' => $data['contact_info'] ?? $item->contact_info,
      ]);

      // Process new images if provided
      if (!empty($images)) {
        $this->processItemImages($item, $images);
      }

      return $item->load(['category', 'user', 'images']);
    });
  }

  /**
   * Verify an item (admin action).
   */
  public function verifyItem(Item $item, ?string $adminNotes = null): Item
  {
    $item->markAsVerified($adminNotes);

    // Send notification to user about verification
    $item->user->notify(new \App\Notifications\ItemVerifiedNotification($item));

    return $item->load(['category', 'user', 'images']);
  }

  /**
   * Reject an item (admin action).
   */
  public function rejectItem(Item $item, ?string $adminNotes = null): Item
  {
    $item->markAsRejected($adminNotes);

    // Send notification to user about rejection
    $item->user->notify(new \App\Notifications\ItemRejectedNotification($item));

    return $item->load(['category', 'user', 'images']);
  }

  /**
   * Mark an item as resolved.
   */
  public function resolveItem(Item $item): Item
  {
    $item->markAsResolved();

    return $item->load(['category', 'user', 'images']);
  }

  /**
   * Delete an item and its associated images.
   */
  public function deleteItem(Item $item): bool
  {
    return DB::transaction(function () use ($item) {
      // Delete associated images from storage
      foreach ($item->images as $image) {
        $this->deleteImageFile($image->filename);
      }

      // Delete the item (images will be deleted via cascade)
      return $item->delete();
    });
  }

  /**
   * Search items with filters.
   */
  public function searchItems(array $filters = [], int $perPage = 15)
  {
    return $this->itemRepository->searchItems(
      query: $filters['query'] ?? null,
      type: $filters['type'] ?? null,
      categoryId: $filters['category_id'] ?? null,
      location: $filters['location'] ?? null,
      startDate: $filters['start_date'] ?? null,
      endDate: $filters['end_date'] ?? null,
      perPage: $perPage
    );
  }

  /**
   * Get items for admin with filters.
   */
  public function getAdminItems(array $filters = [], int $perPage = 15)
  {
    return $this->itemRepository->adminSearchItems(
      query: $filters['query'] ?? null,
      status: $filters['status'] ?? null,
      type: $filters['type'] ?? null,
      categoryId: $filters['category_id'] ?? null,
      perPage: $perPage
    );
  }

  /**
   * Get user's items.
   */
  public function getUserItems(int $userId, int $perPage = 15)
  {
    return $this->itemRepository->getItemsByUser($userId, $perPage);
  }

  /**
   * Get similar items.
   */
  public function getSimilarItems(Item $item, int $limit = 5)
  {
    return $this->itemRepository->getSimilarItems($item, $limit);
  }

  /**
   * Get dashboard statistics.
   */
  public function getDashboardStatistics(): array
  {
    return $this->itemRepository->getStatistics();
  }

  /**
   * Get category statistics.
   */
  public function getCategoryStatistics()
  {
    return $this->itemRepository->getCategoryStatistics();
  }

  /**
   * Get items trend data.
   */
  public function getItemsTrend(int $days = 30)
  {
    return $this->itemRepository->getItemsTrend($days);
  }

  /**
   * Validate item data.
   */
  private function validateItemData(array $data, bool $isUpdate = false): void
  {
    $rules = Item::validationRules($isUpdate);
    $messages = Item::validationMessages();

    $validator = Validator::make($data, $rules, $messages);

    if ($validator->fails()) {
      throw new \Illuminate\Validation\ValidationException($validator);
    }
  }

  /**
   * Validate item data for partial updates.
   */
  private function validateItemDataForUpdate(array $data): void
  {
    $rules = [];
    $messages = Item::validationMessages();

    // Only validate fields that are present in the data
    if (isset($data['title'])) {
      $rules['title'] = 'required|string|max:255';
    }
    if (isset($data['description'])) {
      $rules['description'] = 'required|string|min:10|max:2000';
    }
    if (isset($data['category_id'])) {
      $rules['category_id'] = 'required|exists:categories,id';
    }
    if (isset($data['type'])) {
      $rules['type'] = ['required', Rule::in(['lost', 'found'])];
    }
    if (isset($data['location'])) {
      $rules['location'] = 'required|string|max:255';
    }
    if (isset($data['date_occurred'])) {
      $rules['date_occurred'] = 'required|date|before_or_equal:today';
    }
    if (isset($data['contact_info'])) {
      $rules['contact_info'] = 'required|array';
      $rules['contact_info.method'] = ['required', Rule::in(['email', 'phone', 'both'])];
      $rules['contact_info.email'] = 'required_if:contact_info.method,email,both|email|nullable';
      $rules['contact_info.phone'] = 'required_if:contact_info.method,phone,both|string|nullable';
    }

    if (!empty($rules)) {
      $validator = Validator::make($data, $rules, $messages);

      if ($validator->fails()) {
        throw new \Illuminate\Validation\ValidationException($validator);
      }
    }
  }

  /**
   * Validate uploaded images.
   */
  private function validateImages(array $images): void
  {
    $rules = [
      'images' => 'array|max:5',
      'images.*' => 'image|mimes:jpeg,png,gif|max:2048', // 2MB max
    ];

    $messages = [
      'images.max' => 'You can upload a maximum of 5 images.',
      'images.*.image' => 'Each file must be an image.',
      'images.*.mimes' => 'Images must be in JPEG, PNG, or GIF format.',
      'images.*.max' => 'Each image must be smaller than 2MB.',
    ];

    $validator = Validator::make(['images' => $images], $rules, $messages);

    if ($validator->fails()) {
      throw new \Illuminate\Validation\ValidationException($validator);
    }
  }

  /**
   * Process and store item images.
   */
  private function processItemImages(Item $item, array $images): void
  {
    foreach ($images as $image) {
      if ($image instanceof UploadedFile && $image->isValid()) {
        $this->storeItemImage($item, $image);
      }
    }
  }

  /**
   * Store a single item image.
   */
  private function storeItemImage(Item $item, UploadedFile $image): ItemImage
  {
    // Generate unique filename
    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

    // Store the original image
    $path = $image->storeAs('items/' . $item->id, $filename, 'public');

    // Create optimized version if the image library is available
    $this->createOptimizedImage($image, $path);

    // Create database record
    return ItemImage::create([
      'item_id' => $item->id,
      'filename' => $filename,
      'original_name' => $image->getClientOriginalName(),
      'mime_type' => $image->getMimeType(),
      'size' => $image->getSize(),
    ]);
  }

  /**
   * Create optimized version of the image.
   */
  private function createOptimizedImage(UploadedFile $image, string $storedPath): void
  {
    try {
      // Only create optimized version if Intervention Image is available
      if (class_exists('Intervention\Image\Facades\Image')) {
        $fullPath = Storage::disk('public')->path($storedPath);

        // Resize image if it's too large (max 1200px width)
        $img = Image::make($fullPath);

        if ($img->width() > 1200) {
          $img->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
          });

          $img->save($fullPath, 85); // 85% quality
        }
      }
    } catch (\Exception $e) {
      // If image optimization fails, continue without it
      // Log the error if needed
    }
  }

  /**
   * Delete an image file from storage.
   */
  private function deleteImageFile(string $filename): void
  {
    try {
      // Find the item directory by searching for the file
      $files = Storage::disk('public')->allFiles('items');

      foreach ($files as $file) {
        if (basename($file) === $filename) {
          Storage::disk('public')->delete($file);
          break;
        }
      }
    } catch (\Exception $e) {
      // Log error if needed, but don't fail the operation
    }
  }

  /**
   * Remove a specific image from an item.
   */
  public function removeItemImage(Item $item, int $imageId): bool
  {
    $image = $item->images()->find($imageId);

    if (!$image) {
      return false;
    }

    return DB::transaction(function () use ($image) {
      // Delete file from storage
      $this->deleteImageFile($image->filename);

      // Delete database record
      return $image->delete();
    });
  }

  /**
   * Get items requiring admin attention.
   */
  public function getItemsRequiringAttention(int $daysOld = 7)
  {
    return $this->itemRepository->getItemsRequiringAttention($daysOld);
  }

  /**
   * Bulk update item statuses (admin action).
   */
  public function bulkUpdateStatus(array $itemIds, string $status, ?string $adminNotes = null): int
  {
    $validStatuses = ['pending', 'verified', 'rejected', 'resolved'];

    if (!in_array($status, $validStatuses)) {
      throw new \InvalidArgumentException('Invalid status provided.');
    }

    // Get items before updating to send notifications
    $items = Item::with('user')->whereIn('id', $itemIds)->get();

    $updateData = [
      'status' => $status,
      'admin_notes' => $adminNotes,
    ];

    if ($status === 'verified') {
      $updateData['verified_at'] = now();
    } elseif ($status === 'resolved') {
      $updateData['resolved_at'] = now();
    }

    $count = Item::whereIn('id', $itemIds)->update($updateData);

    // Send notifications to users
    if ($status === 'verified') {
      foreach ($items as $item) {
        $item->refresh(); // Refresh to get updated data
        $item->user->notify(new \App\Notifications\ItemVerifiedNotification($item));
      }
    } elseif ($status === 'rejected') {
      foreach ($items as $item) {
        $item->refresh();
        $item->user->notify(new \App\Notifications\ItemRejectedNotification($item));
      }
    }

    return $count;
  }

  /**
   * Get public item by ID with validation.
   */
  public function getPublicItem(int $id): ?Item
  {
    return $this->itemRepository->findPublicItem($id);
  }

  /**
   * Get item by ID with relationships (for admin/owner).
   */
  public function getItemWithRelations(int $id): ?Item
  {
    return $this->itemRepository->findWithRelations($id);
  }

  /**
   * Check if user can edit the item.
   */
  public function canUserEditItem(Item $item, $user): bool
  {
    // User can edit their own items if they're still pending
    return $item->user_id === $user->id && $item->status === 'pending';
  }

  /**
   * Get recent public items.
   */
  public function getRecentItems(int $limit = 10)
  {
    return $this->itemRepository->getRecentItems($limit);
  }

  /**
   * Get items by category.
   */
  public function getItemsByCategory(int $categoryId, int $perPage = 15)
  {
    return $this->itemRepository->getItemsByCategory($categoryId, $perPage);
  }
}
