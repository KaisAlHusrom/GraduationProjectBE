<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableColumn extends Model
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
