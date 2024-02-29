<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TablesRelationship extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'main_column_id',
        'linked_column_id',
        'relation_type_id',
    ];

    /**
     * Get the main column of the relationship.
     */
    public function mainColumn()
    {
        return $this->belongsTo(TableColumn::class, 'main_column_id');
    }

    /**
     * Get the linked column of the relationship.
     */
    public function linkedColumn()
    {
        return $this->belongsTo(TableColumn::class, 'linked_column_id');
    }

    /**
     * Get the type of relationship.
     */
    public function relationType()
    {
        return $this->belongsTo(RelationType::class);
    }
}
