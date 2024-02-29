<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Component extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'component_title',
        'component_description',
        'component_image',
        'is_template',
    ];


    public function sections()
    {
        // Define a many-to-many relationship with the Section model
        return $this->belongsToMany(Section::class, 'sections_components')
            ->withPivot('sequence_number')
            ->withTimestamps(); // Include pivot column
    }

    public function elements()
    {
        // Define a many-to-many relationship with the Element model
        return $this->belongsToMany(Element::class, 'components_elements')
            ->withPivot('element_content', 'sequence_number') // Include pivot columns
            ->withTimestamps();
    }

    //get linked styles
    public function styles()
    {
        return $this->hasMany(LinkedStyleProp::class, "component_id");
    }
}
