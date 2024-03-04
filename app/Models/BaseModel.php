<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Apply a default ordering based on the created_at column
        static::addGlobalScope('order', function ($query) {
            $query->orderBy('created_at', 'desc');
        });
    }


}
