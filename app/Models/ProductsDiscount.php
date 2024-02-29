<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsDiscount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'discount_code',
        'discount_percentage',
        'discount_start_date',
        'discount_end_date',
        'discount_title',
        'discount_occasion',
        'discount_description',
    ];

    /**
     * Get the user associated with the discount.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product associated with the discount.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
