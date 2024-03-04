<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlanFeature extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_plan_feature_name',
        'payment_plan_feature_image',
        'payment_plan_feature_description',
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
     * Get the payment plans associated with the feature.
     */
    public function plans()
    {
        return $this->belongsToMany(PaymentPlan::class, 'payment_plans_features');
    }
}
