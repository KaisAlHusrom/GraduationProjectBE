<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'page_title',
        'page_description',
        'page_image',
        'is_template',
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

    // Define a many-to-many relationship with the WebProject model
    public function webProjects()
    {
        return $this->belongsToMany(WebProject::class, 'web_projects_pages')
            ->withPivot('page_router', 'page_protector_route_user_column')
            ->withTimestamps(); // Include pivot columns
    }

    public function sections()
    {
        // Define a many-to-many relationship with the Section model
        return $this->belongsToMany(Section::class, 'pages_sections')
            ->withPivot('sequence_number', 'section_link_id') // Include pivot columns
            ->withTimestamps();
    }
}
