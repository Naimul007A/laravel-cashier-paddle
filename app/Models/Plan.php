<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
