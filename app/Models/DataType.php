<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data_type_name',
        'data_type_description',
    ];

    /**
     * Get the columns associated with the data type.
     */
    public function columns()
    {
        return $this->hasMany(TableColumn::class, "data_type_id");
    }
}
