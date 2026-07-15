<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable=['code','type','value','status'];

    public static function findByCode($code){
        return self::where('code',$code)->first();
    }
    public function discount($total){
        if($this->type=="fixed"){
            return $this->value;
        }
        elseif($this->type=="percent"){
            return ($this->value /100)*$total;
        }
        else{
            return 0;
        }
    }

     protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid(float $orderAmount): array
    {
        if (!$this->is_active) {
            return [false, 'Promo code is not active'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return [false, 'Promo code has expired'];
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return [false, 'Promo code usage limit reached'];
        }

        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return [false, "Minimum order amount is {$this->min_order_amount}"];
        }

        return [true, null];
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->type === 'percent') {
            return round($orderAmount * ($this->value / 100), 2);
        }

        return min($this->value, $orderAmount); // fixed discount order se zyada na ho
    }
}
