<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends BaseModel
{
    use HasFactory, SoftDeletes;

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_title',
        'section_description',
        'section_image',
        'is_template',
    ];

    public function pages()
    {
        // Define a many-to-many relationship with the Page model
        return $this->belongsToMany(Page::class, 'pages_sections')
            ->withPivot('sequence_number', 'section_link_id')
            ->withTimestamps(); // Include pivot columns
    }

    public function components()
    {
        // Define a many-to-many relationship with the Component model
        return $this->belongsToMany(Component::class, 'sections_components')
            ->withPivot('sequence_number') // Include pivot column
            ->withTimestamps();
    }

    //get linked styles
    public function styles()
    {
        return $this->hasMany(LinkedStyleProp::class, "section_id");
    }
}
