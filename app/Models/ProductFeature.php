<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFeature extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'product_id',
        'product_feature_name',
        'product_feature_description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        // Add any other attributes that you want to cast here
    ];

    /**
     * Get the product that owns the feature.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, "product_id");
    }
}
