<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElementsPropsValue extends BaseModel
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

    public function componentElement()
    {
        // Define a many-to-one relationship with ComponentElement model
        return $this->belongsTo(ComponentsElement::class, 'component_element_id');
    }

    public function elementTypeProp()
    {
        // Define a many-to-one relationship with ElementTypeProp model
        return $this->belongsTo(ElementTypesProp::class, 'element_type_prop_id');
}
}
