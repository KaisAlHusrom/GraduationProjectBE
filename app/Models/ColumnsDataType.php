<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColumnsDataType extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'column_id',
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
     * Get the column associated with the column data type.
     */
    public function column()
    {
        return $this->belongsTo(TableColumn::class, 'column_id');
    }

    /**
     * Get the data type associated with the column data type.
     */
    public function dataType()
    {
        return $this->belongsTo(DataType::class, 'data_type_id');
    }
}
