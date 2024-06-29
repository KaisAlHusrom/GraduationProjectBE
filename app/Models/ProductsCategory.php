<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsCategory extends Model
{
    use HasFactory;

    protected $table = "products_categories";

    protected $fillable = [
        'product_id',
        'category_id',
    ];

    //many-to-one to product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    //many-to-one to category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

}
