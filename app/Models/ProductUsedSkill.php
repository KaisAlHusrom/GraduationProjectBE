<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUsedSkill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'product_used_skill_name',
        'how_used',
    ];

    /**
     * Get the product that owns the used skill.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
