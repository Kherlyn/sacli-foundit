<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'filename',
        'original_name',
        'mime_type',
        'size'
    ];

    /**
     * Get the item that owns the image.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
