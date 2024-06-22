<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignedElementsPropValue extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'design_id',
        'element_prop_id',
        'design_prop_value',
    ];

    /**
     * Get the design associated with the prop value.
     */
    public function design()
    {
        return $this->belongsTo(Design::class, 'design_id');
    }

    /**
     * Get the prop associated with the prop value.
     */
    public function elementProp()
    {
        return $this->belongsTo(ElementProp::class, 'element_prop_id');
    }
}
