<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElementType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'element_type_name',
        'element_type_description',
        'is_child',
        'children_id',
    ];

    /**
     * Get the parent element type if this element type is a child.
     */
    public function parent()
    {
        return $this->belongsTo(ElementType::class, 'children_id');
    }

    /**
     * Get the child element types if this element type is a parent.
     */
    public function children()
    {
        return $this->hasMany(ElementType::class, 'children_id');
    }

    /**
     * get element props of this type
     */
    public function elementProps()
    {
        return $this->belongsToMany(ElementProp::class, 'element_types_props');
    }
}
