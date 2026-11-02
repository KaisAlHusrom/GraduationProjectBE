<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Database extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'database_name',
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
     * Get the web project that owns the database.
     */
    public function webProject()
    {
        return $this->belongsTo(WebProject::class);
    }


    /**
     * Get database tables.
     */
    public function tables()
    {
        return $this->hasMany(DatabaseTable::class);
    }
}
