<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentsElement extends Model
{
    use HasFactory;

    public function propValues()
    {
        // Define a one-to-many relationship with ElementPropValue model
        return $this->hasMany(ElementsPropsValue::class, 'component_element_id');
    }
}
