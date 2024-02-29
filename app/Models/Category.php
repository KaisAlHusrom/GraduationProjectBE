<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_name',
        'category_image',
        'category_description',
    ];

    /**
     * Get the products associated with the category.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_categories');
    }
}
