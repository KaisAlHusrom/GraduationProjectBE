<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'product_media_name',
        'is_video',
    ];

    /**
     * Get the product that owns the media.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
