<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElementTypesProp extends Model
{
    use HasFactory;

    public function values()
    {
        // Define a one-to-many relationship with ElementPropValue model
        return $this->hasMany(ElementsPropsValue::class, 'element_type_prop_id');
    }
}
