<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    //cats
    protected $casts = [
        'id' => 'string',
        // Add any other attributes that you want to cast here
    ];

    //* to ensure that all boolean values, will be return as boolean, and all id columns will return as string
    public function getCasts(): array
    {
        $casts = parent::getCasts();

        // Ensure 'id' is always cast to 'string'
        $casts['id'] = 'string';

        // Dynamically add boolean casts for attributes starting with 'is_' or 'not_'
        foreach ($this->attributes as $key => $value) {
            if (preg_match('/^(is_|not_)/', $key)) {
                $casts[$key] = 'boolean';
            }
        }

        return $casts;
    }

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

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Set default UUID for the 'id' attribute before saving the model
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }



}
