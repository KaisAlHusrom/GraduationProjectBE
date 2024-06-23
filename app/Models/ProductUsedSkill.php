<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUsedSkill extends BaseModel
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
        'product_used_skill_name',
        'how_used',
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
     * Get the product that owns the used skill.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
