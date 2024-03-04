<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElementTypesProp extends BaseModel
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

    public function values()
    {
        // Define a one-to-many relationship with ElementPropValue model
        return $this->hasMany(ElementsPropsValue::class, 'element_type_prop_id');
    }
}
