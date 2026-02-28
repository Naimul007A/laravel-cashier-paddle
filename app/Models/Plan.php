<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class Plan extends Model {

    protected $fillable = [
        'paddle_id',
        'name',
        'price',
        'paddle_monthly_price_id',
        'paddle_yearly_price_id',
        'yearly_offer_percentage',
        'rate_limit',
        'agent_limit',
        'members_limit',
        'credit_limit',
        'free_limit',
        'extra_credit',
    ];
    protected static function boot() {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }
}
