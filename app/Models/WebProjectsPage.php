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
    protected $casts = [
        'id' => 'string',
        // Add any other attributes that you want to cast here
    ];
}
