<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_name',
        'user_id',
        'product_short_description',
        'product_long_description',
        'product_price',
        'product_main_image_name',
        'product_file_name',
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

    //many-to-one to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //one-to-many to product Media
    public function productMedia()
    {
        return $this->hasMany(ProductMedia::class);
    }

    //one-to-many to product Media
    public function productUsedSkills()
    {
        return $this->hasMany(ProductUsedSkill::class);
    }

    //one-to-many to product Media
    public function productFeatures()
    {
        return $this->hasMany(ProductFeature::class, "product_id");
    }

    // one-to-many
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the categories associated with the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'products_categories');
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the products associated with the category.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'sub_total')
            ->withTimestamps();
    }

    /**
     * Get the messages associated with the product.
     */
    public function messages()
    {
        return $this->hasMany(UserProductOwnerMessage::class);
    }
}
