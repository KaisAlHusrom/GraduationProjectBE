<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableColumn extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'column_name',
        'is_pk',
        'is_fk',
        'data_type_id',
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
     * Get the table that owns the column.
     */
    public function table()
    {
        return $this->belongsTo(DatabaseTable::class);
    }

    /**
     * Get the table that owns the column.
     */
    public function dataType()
    {
        return $this->belongsTo(DataType::class);
    }
}
