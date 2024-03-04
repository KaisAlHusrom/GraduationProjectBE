<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Element extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'element_type_id',
        'children_id',
        'element_title',
        'element_description',
        'element_image',
        'is_template',
        'sequence_number',
    ];

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
     * Get the element type that owns the element.
     */
    public function elementType()
    {
        return $this->belongsTo(ElementType::class, 'element_type_id');
    }

    /**
     * Get the children elements of the element if exist.
     */
    public function children()
    {
        return $this->hasMany(Element::class, 'children_id');
    }

    /**
     * Get the parent element of the element.
     */
    public function parent()
    {
        return $this->belongsTo(Element::class, 'children_id');
    }


    public function components()
    {
        // Define a many-to-many relationship with the Component model
        return $this->belongsToMany(Component::class, 'components_elements')
            ->withPivot('element_content', 'sequence_number')
            ->withTimestamps(); // Include pivot columns
    }

    //get linked styles
    public function styles()
    {
        return $this->hasMany(LinkedStyleProp::class, "element_id");
    }
}
