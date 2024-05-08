<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Design extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'element_type_id',
        'parent_id',
        'design_title',
        'design_description',
        'design_image',
        'is_template',
        'is_child',
        'sequence_number',
        'element_content'
    ];

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
     * Get the element type that owns the element.
     */
    public function elementType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ElementType::class, 'element_type_id');
    }

    /**
     * Get the children elements of the element if exist.
     */
    public function children()
    {
        return $this->hasMany(Design::class, 'parent_id');
    }

    /**
     * Get the parent element of the element.
     */
    public function parent()
    {
        return $this->belongsTo(Design::class, 'parent_id');
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
