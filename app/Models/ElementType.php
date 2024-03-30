<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElementType extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'is_child' => 'boolean',
        // Add any other attributes that you want to cast here
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'element_type_name',
        'element_type_description',
        'is_child',
        'parent_id',
    ];



    /**
     * Get the parent element type if this element type is a child.
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(ElementType::class, 'parent_id');
    }

    /**
     * Get the child element types if this element type is a parent.
     */
    public function children()
    {
        return $this->hasMany(ElementType::class, 'parent_id');
    }

    /**
     * get element props of this type
     */
    public function elementProps() : BelongsToMany
    {
        return $this->belongsToMany(ElementProp::class, 'element_types_props');
    }
}
