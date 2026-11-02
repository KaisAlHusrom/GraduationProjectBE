<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableCell extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cell_data',
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
     * Get the column that owns the data cell.
     */
    public function column()
    {
        return $this->belongsTo(TableColumn::class);
    }

    /**
     * Get the row that owns the data cell.
     */
    public function row()
    {
        return $this->belongsTo(TableRow::class);
    }
}
