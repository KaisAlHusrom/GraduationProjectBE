<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentPlan extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_plan_title',
        'payment_plan_monthly_price',
        'payment_plan_yearly_price',
        'payment_plan_description',
        'is_active',
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
     * Get the features associated with the payment plan.
     */
    public function features()
    {
        return $this->HasMany(PaymentPlanFeature::class, 'payment_plan_id');
    }

    /**
     * Get the users associated with the payment plan.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_payment_plans')
            ->withPivot('status', 'is_active', 'bill_date')
            ->withTimestamps();
    }
}
