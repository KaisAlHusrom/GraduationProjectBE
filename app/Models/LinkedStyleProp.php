<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LinkedStyleProp extends BaseModel
{
    use HasFactory, SoftDeletes;

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_id',
        'component_id',
        'element_id',
        'style_status_id',
        'style_responsive_breakpoint_id',
        'style_prop_id',
        'style_prop_value',
    ];

    /**
     * Get the section associated with the linked style prop.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the component associated with the linked style prop.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get the element associated with the linked style prop.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }

    /**
     * Get the style status associated with the linked style prop.
     */
    public function styleStatus(): BelongsTo
    {
        return $this->belongsTo(StyleStatus::class);
    }

    /**
     * Get the style responsive breakpoint associated with the linked style prop.
     */
    public function styleResponsiveBreakpoint(): BelongsTo
    {
        return $this->belongsTo(StyleResponsiveBreakpoint::class);
    }

    /**
     * Get the style prop associated with the linked style prop.
     */
    public function styleProp(): BelongsTo
    {
        return $this->belongsTo(StyleProp::class);
    }
}
