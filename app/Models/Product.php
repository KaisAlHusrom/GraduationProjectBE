<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;


    //many-to-one to user
    public function user()
    {
        return $this->belongsTo(User::class);
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
        return $this->hasMany(ProductFeature::class);
    }

    // one-to-many
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    // MANY TO MANY
    public function categories()
    {
        return $this->belongsToMany(Category::class, "products_categories");
    }


}
