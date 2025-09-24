<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'type',
        'status',
        'location',
        'date_occurred',
        'contact_info',
        'user_id',
        'admin_notes',
        'verified_at',
        'resolved_at'
    ];

    protected $casts = [
        'date_occurred' => 'date',
        'verified_at' => 'datetime',
        'resolved_at' => 'datetime',
        'contact_info' => 'array'
    ];

    /**
     * Get the user that owns the item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the item.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ItemImage::class);
    }

    /**
     * Scope a query to only include verified items.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope a query to only include pending items.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include public items (verified).
     */
    public function scopePublic($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope a query to search items by title and description.
     */
    public function scopeSearch($query, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        return $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%");
        });
    }

    /**
     * Scope a query to filter by item type (lost/found).
     */
    public function scopeOfType($query, $type)
    {
        if (empty($type)) {
            return $query;
        }

        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        if (empty($categoryId)) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('date_occurred', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date_occurred', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Scope a query to filter by location.
     */
    public function scopeNearLocation($query, $location)
    {
        if (empty($location)) {
            return $query;
        }

        return $query->where('location', 'LIKE', "%{$location}%");
    }

    /**
     * Get validation rules for item creation/update.
     */
    public static function validationRules($isUpdate = false)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10|max:2000',
            'category_id' => 'required|exists:categories,id',
            'type' => ['required', Rule::in(['lost', 'found'])],
            'location' => 'required|string|max:255',
            'date_occurred' => 'required|date|before_or_equal:today',
            'contact_info' => 'required|array',
            'contact_info.method' => ['required', Rule::in(['email', 'phone', 'both'])],
            'contact_info.email' => 'required_if:contact_info.method,email,both|email|nullable',
            'contact_info.phone' => 'required_if:contact_info.method,phone,both|string|nullable',
        ];

        if ($isUpdate) {
            $rules['status'] = ['sometimes', Rule::in(['pending', 'verified', 'rejected', 'resolved'])];
            $rules['admin_notes'] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    /**
     * Get validation messages for item validation.
     */
    public static function validationMessages()
    {
        return [
            'title.required' => 'The item title is required.',
            'title.max' => 'The item title cannot exceed 255 characters.',
            'description.required' => 'The item description is required.',
            'description.min' => 'The item description must be at least 10 characters.',
            'description.max' => 'The item description cannot exceed 2000 characters.',
            'category_id.required' => 'Please select a category for the item.',
            'category_id.exists' => 'The selected category is invalid.',
            'type.required' => 'Please specify if this is a lost or found item.',
            'type.in' => 'The item type must be either lost or found.',
            'location.required' => 'The location is required.',
            'location.max' => 'The location cannot exceed 255 characters.',
            'date_occurred.required' => 'The date when the item was lost/found is required.',
            'date_occurred.date' => 'Please provide a valid date.',
            'date_occurred.before_or_equal' => 'The date cannot be in the future.',
            'contact_info.required' => 'Contact information is required.',
            'contact_info.method.required' => 'Please specify a contact method.',
            'contact_info.method.in' => 'Contact method must be email, phone, or both.',
            'contact_info.email.required_if' => 'Email address is required when email contact is selected.',
            'contact_info.email.email' => 'Please provide a valid email address.',
            'contact_info.phone.required_if' => 'Phone number is required when phone contact is selected.',
        ];
    }

    /**
     * Check if the item is owned by the given user.
     */
    public function isOwnedBy($user)
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if the item is verified.
     */
    public function isVerified()
    {
        return $this->status === 'verified';
    }

    /**
     * Check if the item is pending verification.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the item is resolved.
     */
    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    /**
     * Mark the item as verified.
     */
    public function markAsVerified($adminNotes = null)
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'admin_notes' => $adminNotes,
        ]);

        // Send notification
        app(\App\Services\NotificationService::class)->sendItemVerifiedNotification($this);
    }

    /**
     * Mark the item as rejected.
     */
    public function markAsRejected($adminNotes = null)
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $adminNotes,
        ]);

        // Send notification
        app(\App\Services\NotificationService::class)->sendItemRejectedNotification($this);
    }

    /**
     * Mark the item as resolved.
     */
    public function markAsResolved()
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        // Send notification
        app(\App\Services\NotificationService::class)->sendItemResolvedNotification($this);
    }
}
