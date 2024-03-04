<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StylePropsCategory extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_name', 'category_description'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        // Add any other attributes that you want to cast here
    ];

    public function styleProps()
    {
        return $this->hasMany(StyleProp::class, 'category_id');
    }
}
