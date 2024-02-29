<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StyleProp extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'style_prop_normal_name',
        'style_prop_css_name',
        'style_prop_image',
        'style_prop_description',
        'style_prop_value_type',
        'is_section',
        'is_component',
        'is_element',
    ];

    /**
     * Get the category that owns the style property.
     */
    public function category()
    {
        return $this->belongsTo(StylePropsCategory::class, 'category_id');
    }

    /**
     * Get the options
     */
    public function options()
    {
        return $this->hasMany(StylePropsValue::class, 'style_prop_id');
    }
}
