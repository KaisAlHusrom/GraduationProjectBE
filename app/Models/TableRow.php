<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableRow extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // Add any fillable attributes here if needed
    ];

    /**
     * Get the table that owns the row.
     */
    public function table()
    {
        return $this->belongsTo(DatabaseTable::class);
    }
}
