<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebProjectsPage extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $fillable = [
        'web_project_id',
        'page_id',
        'page_router',
        'page_protector_route_user_column',
    ];


    protected $casts = [
        'id' => 'string',
        // Add any other attributes that you want to cast here
    ];

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'web_projects_pages')
            ->withPivot('page_router', 'page_protector_route_user_column')
            ->withTimestamps(); // Include pivot columns
    }
    public function webProject()
    {
        return $this->belongsToMany(webProject::class)
            ->withTimestamps(); // Include pivot columns
    }


}
