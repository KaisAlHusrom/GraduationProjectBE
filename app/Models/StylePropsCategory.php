<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StylePropsCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_name', 'category_description'];

    public function styleProps()
    {
        return $this->hasMany(StyleProp::class, 'category_id');
    }
}
