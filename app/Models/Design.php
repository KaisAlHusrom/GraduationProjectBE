<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Design extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'element_type_id',
        'parent_id',
        'category_id',
        'design_title',
        'design_description',
        'design_image',
        'is_template',
        'is_child',
        'design_type',
        'sequence_number',
        'element_content'
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
     * Get the element type that owns the design.
     */
    public function elementType(): BelongsTo
    {
        return $this->belongsTo(ElementType::class, 'element_type_id');
    }

    /**
     * Get the element type that owns the design.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    /**
     * Get the children elements of the element if exist.
     */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Design::class, 'parent_id')
            ->with(
                [
                    'styles.styleProp',
                    'styles.styleResponsiveBreakpoint',
                    'styles.styleStatus',
                    'elementType',
                    'styles',
                    'parent',
                    'pages',
                ]
            );
    }


    /**
     * Get the parent element of the element.
     */
    public function parent()
    {
        return $this->belongsTo(Design::class, 'parent_id');
    }

    /**
     * Get the parent element of the element.
     */
    public function category()
    {
        return $this->belongsTo(DesignCategory::class, 'category_id');
    }


    public function pages()
    {
        // Define a many-to-many relationship with the Component model
        return $this->belongsToMany(Page::class, 'pages_sections')
            ->withPivot('sequence_number')
            ->withTimestamps(); // Include pivot columns
    }

    //get linked styles
    public function styles()
    {
        return $this->hasMany(LinkedStyleProp::class, "design_id");
    }
}
