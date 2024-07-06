<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebProject extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'project_title',
        'project_logo',
        'project_type',
        'project_job',
        'project_description',
        'is_template',
        'is_own_project',
        'is_published
        '
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

    /**
     * Get the user who owns the project.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define a many-to-many relationship with the Page model
    public function pages()
    {
        return $this->hasMany(Page::class, 'web_project_id')->with("designs");
    }

    /**
     * Get the databases associated with the web project.
     */
    public function databases()
    {
        return $this->hasMany(Database::class);
    }
    public function designs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Design::class, 'web_project_id')
            ->with(
                [
                    'styles.styleProp',
                    'styles.styleResponsiveBreakpoint',
                    'styles.styleStatus',
                    'children.children.children.children.children.children.children.children.children.children',
                    'elementType',
                    'styles',
                    'parent',
                    'pages',
                ]
            );
    }
}
