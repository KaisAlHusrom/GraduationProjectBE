<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StylePropsValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'style_prop_id',
        'style_prop_value_normal_name',
        'style_prop_value_css_name',
        'style_prop_value_image',
    ];

    /**
     * Get the style property that owns the value.
     */
    public function styleProp(): BelongsTo
    {
        return $this->belongsTo(StyleProp::class);
    }
}
