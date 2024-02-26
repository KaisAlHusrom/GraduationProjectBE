<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUsedSkill extends Model
{
    use HasFactory;

    //many-to-one to product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
