<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Plan;
use App\Models\Superadmin;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->plans();

        if ($email = env('SUPERADMIN_EMAIL')) {
            Superadmin::firstOrCreate(['email' => $email], [
                'name' => env('SUPERADMIN_NAME', 'Super Admin'),
                'password' => env('SUPERADMIN_PASSWORD'),
            ]);
        }

        if (app()->isLocal()) {
            $this->demoBusinesses();
        }
    }

    /** Demo placeholders, only created when no plans exist. Replace them in Super admin > Plans. */
    private function plans(): void
    {
        if (Plan::exists()) {
            return;
        }

        $send = 'Messages to your customers\' phones';

        foreach ([
            ['trial', 'Free trial', 0, 'month', 'Try it with no card.', ['Your own app', $send, 'Up to 25 customers', 'Email support'], 1],
            ['starter', 'Starter', 29, 'month', 'For a single location getting started.', ['Your own app', $send, 'Up to 500 customers', 'Unlimited messages', 'Email support'], 2],
            ['growth', 'Growth', 59, 'month', 'For busy shops with regulars.', ['Everything in Starter', 'Up to 2,500 customers', 'Scheduled messages', 'Priority support'], 3],
            ['pro', 'Pro', 99, 'month', 'For several locations or big followings.', ['Everything in Growth', 'Up to 10,000 customers', 'Team accounts', 'Phone support'], 4],
        ] as [$code, $name, $price, $interval, $description, $features, $order]) {
            Plan::create(['code' => $code, 'name' => $name, 'price_cents' => $price * 100, 'interval' => $interval, 'description' => $description, 'features' => $features, 'sort_order' => $order, 'is_public' => true]);
        }
    }

    private function demoBusinesses(): void
    {
        $plan = Plan::where('code', 'starter')->first() ?? Plan::first();

        foreach ([
            ['demo-barber', 'Demo Barber', 'owner@demo.test', []],
            ['hoshmint', 'Hoshmint', 'owner@hoshmint.test', ['hoshmint.com', 'www.hoshmint.com']],
        ] as [$slug, $name, $email, $domains]) {
            $business = Business::firstOrCreate(['slug' => $slug], ['name' => $name, 'short_name' => $name, 'status' => 'active', 'plan_id' => $plan?->id]);
            User::firstOrCreate(['email' => $email], ['business_id' => $business->id, 'name' => $name.' Owner', 'role' => 'owner', 'password' => 'password']);
            foreach ($domains as $domain) {
                $business->domains()->firstOrCreate(['domain' => $domain]);
            }
        }
    }
}
