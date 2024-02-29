<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElementProp extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'element_prop_name',
        'element_prop_description',
    ];

    /**
     * get element types of this props
     */
    public function elementTypes()
    {
        return $this->belongsToMany(ElementType::class, 'element_types_props');
    }
}
