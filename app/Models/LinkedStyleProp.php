<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LinkedStyleProp extends BaseModel
{
    use HasFactory;

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
        'design_id',
        'style_status_id',
        'style_responsive_breakpoint_id',
        'style_prop_id',
        'style_prop_value',
    ];


    /**
     * Get the element associated with the linked style prop.
     */
    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
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
