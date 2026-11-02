<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StyleProp extends BaseModel
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
        'is_child',
        'parent_id',
        'locateTypes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        "is_section" => "bool",
        "is_component" => "bool",
        "is_element" => "bool",
        "is_child" => "bool",
        // Add any other attributes that you want to cast here
    ];

    /**
     * Get the category that owns the style property.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(StylePropsCategory::class, 'category_id');
    }

    /**
     * Get the options
     */
    public function options(): HasMany
    {
        return $this->hasMany(StylePropsValue::class, 'style_prop_id');
    }

    public function parent() : BelongsTo
    {
        return $this->belongsTo(StyleProp::class, 'parent_id');
    }

    /**
     * Get the child element types if this element type is a parent.
     */
    public function children(): HasMany
    {
        return $this->hasMany(StyleProp::class, 'parent_id');
    }
}
