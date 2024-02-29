<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StyleResponsiveBreakpoint extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'style_responsive_break_point_normal_name',
        'style_responsive_break_point_css_name',
        'style_responsive_break_point_number_value',
        'style_responsive_break_point_image',
        'style_responsive_break_point_description',
    ];
}
