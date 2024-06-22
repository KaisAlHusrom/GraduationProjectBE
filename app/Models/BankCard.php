<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class BankCard extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'card_number',
        'card_holder_name',
        'expiry_date',
        'cvv'
    ];

    protected $hidden = [
//        'card_number',
        'cvv',
    ];

    /**
     * Set the card number.
     *
     * @param  string  $value
     * @return void
     */
    public function setCardNumberAttribute($value): void
    {
        // Encrypt the card number using SHA-256
        $this->attributes['card_number'] = Crypt::encryptString($value);
    }

    /**
     * Set the CVV.
     *
     * @param  string  $value
     * @return void
     */
    public function setCvvAttribute($value): void
    {
        // Encrypt the CVV using SHA-256
        $this->attributes['cvv'] = hash('sha256', $value);
    }

    /**
     * Get the formatted card number.
     *
     * @return string
     */
    public function getCardNumberAttribute(): string
    {
        // Retrieve the original card number (if needed, from a decrypted source)
        $encryptedCardNumber = $this->attributes['card_number'];

        // Since the card number is hashed, assume the original card number is known or stored securely elsewhere
        $originalCardNumber = $this->getOriginalCardNumber($encryptedCardNumber);

        // Mask the card number, keeping the first 4 and last 4 digits visible
        return substr($originalCardNumber, 0, 4) . str_repeat('*', strlen($originalCardNumber) - 8) . substr($originalCardNumber, -4);
    }

    /**
     * This is a placeholder for retrieving the original card number based on the encrypted value.
     * In a real-world scenario, you'd handle decryption and secure storage differently.
     *
     * @param string $encryptedCardNumber
     * @return string
     */
    private function getOriginalCardNumber($encryptedCardNumber): string
    {
        // This function should return the original card number
        // Implement your logic to retrieve the original card number here
        // For example, querying from a secure storage or decryption mechanism
        return  Crypt::decryptString($encryptedCardNumber);
    }

    /**
     * Get the user that owns the bank card.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that owns the plan.
     */
    public function payments()
    {
        return $this->hasMany(UsersPayment::class, 'bank_card_id');
    }
}
