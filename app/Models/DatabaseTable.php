<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseTable extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'table_name',
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
     * Get the database that owns the table.
     */
    public function database()
    {
        return $this->belongsTo(Database::class);
    }


    /**
     * Get the table columns.
     */
    public function columns()
    {
        return $this->hasMany(TableColumn::class);
    }


    /**
     * Get the table rows.
     */
    public function rows()
    {
        return $this->hasMany(TableRow::class);
    }
}
