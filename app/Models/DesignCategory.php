<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesignCategory extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_name',
        'category_image',
        'category_description',
        'design_type',
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
     * Get the designs of the category if exist.
     */
    public function designs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Design::class, 'category_id');
    }

}
