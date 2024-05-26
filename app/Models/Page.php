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
    public function webProject(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(WebProject::class, "web_project_id");
    }


    public function designs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Design::class, 'page_id')
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
