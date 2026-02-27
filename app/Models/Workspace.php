<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Paddle\Billable;

class Workspace extends Model {
    use Billable;
    protected $fillable = [
        'name',
        'slug',
        'owner_id',
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'workspace_users')
            ->withPivot('role', 'status')
            ->withTimestamps();
    }
    /**
     * Paddle email
     */
    public function paddleEmail() {
        return $this->owner->email;
    }
}
