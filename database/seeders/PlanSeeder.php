<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $plans = [
            [
                'paddle_id'               => 'free',
                'name'                    => 'Free',
                'price'                   => 0,
                'yearly_offer_percentage' => 0,
                'rate_limit'              => 10, // requests/min
                'agent_limit'             => 1,  // agents
                'members_limit'           => 1,  // workspace members
                'credit_limit'            => 50, // monthly credits
                'free_limit'              => 50, // free credits
                'extra_credit'            => 0,  // bonus credits
            ],
            [
                'paddle_id'               => 'pro',
                'name'                    => 'Pro',
                'price'                   => 15,
                'yearly_offer_percentage' => 20,
                'rate_limit'              => 60,
                'agent_limit'             => 3,
                'members_limit'           => 5,
                'credit_limit'            => 500,
                'free_limit'              => 100,
                'extra_credit'            => 50,
            ],
            [
                'paddle_id'               => 'pro-plus',
                'name'                    => 'Pro+',
                'price'                   => 30,
                'yearly_offer_percentage' => 25,
                'rate_limit'              => 120,
                'agent_limit'             => 10,
                'members_limit'           => 15,
                'credit_limit'            => 2000,
                'free_limit'              => 300,
                'extra_credit'            => 200,
            ],
            [
                'paddle_id'               => 'master',
                'name'                    => 'Master',
                'price'                   => 99,
                'yearly_offer_percentage' => 30,
                'rate_limit'              => -1, // unlimited
                'agent_limit'             => -1, // unlimited
                'members_limit'           => -1, // unlimited
                'credit_limit'            => -1, // unlimited
                'free_limit'              => -1, // unlimited
                'extra_credit'            => 1000,
            ],
        ];

        foreach ($plans as $plan) {
            \App\Models\Plan::updateOrCreate(
                ['paddle_id' => $plan['paddle_id']],
                $plan
            );
        }
    }
}
