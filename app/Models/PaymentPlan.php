<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentPlan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_plan_title',
        'payment_plan_price',
        'payment_plan_description',
        'is_active',
    ];

    /**
     * Get the features associated with the payment plan.
     */
    public function features()
    {
        return $this->belongsToMany(PaymentPlanFeature::class, 'payment_plans_features');
    }

    /**
     * Get the users associated with the payment plan.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_payment_plans')
            ->withPivot('status', 'expire-date', 'is_active', 'bill_date')
            ->withTimestamps();
    }
}
