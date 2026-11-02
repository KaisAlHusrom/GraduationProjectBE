<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use SoftDeletes;
    use Billable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'profile_image',
        'mobile_number',
        'is_admin',
        'birth_date',
        'total_balance',
        'withdrawable_balance',
        'email',
        'password',
        'free_trial_expiration_date',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => "string",
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    //one-to-many to products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // one-to-many
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the payment plans associated with the user.
     */
    public function paymentPlans()
    {
        return $this->belongsToMany(PaymentPlan::class, 'users_payment_plans')
            ->withPivot('status', 'is_active', 'bill_date')
            ->withTimestamps();
    }

    /**
     * Get the payment plans associated with the user.
     */
    public function userPaymentPlans()
    {
        return $this->hasMany(UsersPaymentPlan::class, 'user_id');
    }

    //get active payment plan
    public function activePaymentPlan() {
        return $this->paymentPlans()->wherePivot('is_active', '=', '1')->first();
    }

    //check plan feature
    public function checkFeature($feature)
    {
        $paymentPlan = $this->activePaymentPlan();

        $planFeatures = Config::get('plans_features.' . strtolower($paymentPlan->payment_plan_title));
        if (!$paymentPlan) {
            return $planFeatures['free'];
        }

        return $planFeatures[$feature] ?? null;
    }

    //get active subscription
    public function activeSubscription() {
        return $this->subscriptions()->where('ends_at', '=', null)->first();
    }


    /**
     * Get the payments for the user.
     */
    public function payments()
    {
        return $this->hasMany(UsersPayment::class);
    }


    /**
     * Get the discounts for the user.
     */
    public function discounts()
    {
        return $this->hasMany(ProductsDiscount::class);
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(UserProductOwnerMessage::class, 'sender_id');
    }

    /**
     * Get the bank cards for the user.
     */
    public function bankCards()
    {
        return $this->hasMany(BankCard::class);
    }

    /**
     * Get the web projects who owns the project.
     */
    public function webProjects()
    {
        return $this->hasMany(WebProject::class, 'user_id');
    }
}
