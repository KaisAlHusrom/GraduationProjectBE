<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends BaseModel
{
    protected $table = 'personal_access_tokens';

    protected $fillable = [
        'tokenable_type',
        'tokenable_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
        'revoked'
    ];

    protected $dates = [
        'last_used_at',
        'expires_at',
    ];

    /**
     * Get the tokenable model that the token belongs to.
     */
    public function tokenable()
    {
        return $this->morphTo();
    }
}
