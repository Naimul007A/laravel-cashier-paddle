<?php
namespace App\Services\Auth;

use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use function Symfony\Component\Clock\now;
use Illuminate\Support\Str;

class AuthService {
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function activeAccount(User $user, $role = 'user') {
        // create a workspace for the user
        $workspaceName = $user->name . "'s Workspace";
        $freePlan      = Plan::where('slug', 'free')->first();
        $today         = Carbon::now();
        $nextBillDate  = $today->copy()->addMonth();
        $workspace     = Workspace::create([
            'name'              => $workspaceName,
            'slug'              => Str::slug($workspaceName),
            'owner_id'          => $user->id,
            'credit_limit'      => $freePlan->credit_limit,
            'free_limit'        => $freePlan->free_limit,
            'rate_limit'        => $freePlan->rate_limit,
            'agent_limit'       => $freePlan->agent_limit,
            'members_limit'     => $freePlan->members_limit,
            'subscribe_at'      => $today,
            'next_bill_date'    => $nextBillDate,
            'next_credit_reset' => $nextBillDate,
        ]);
        // add user into the workspace
        $workspace->members()->create([
            'workspace_id' => $workspace->id,
            'user_id'      => $user->id,
            'role'         => $role,
        ]);
        $workspace->save();

        // return the user
        return $workspace;
    }
}
