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
        Plan::firstOrCreate(['code' => 'app'], ['name' => 'Your App', 'features' => ['push', 'hosted_app']]);

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

    private function demoBusinesses(): void
    {
        $plan = Plan::where('code', 'app')->first();

        foreach ([
            ['demo-barber', 'Demo Barber', 'owner@demo.test', []],
            ['hoshmint', 'Hoshmint', 'owner@hoshmint.test', ['hoshmint.com', 'www.hoshmint.com']],
        ] as [$slug, $name, $email, $domains]) {
            $business = Business::firstOrCreate(['slug' => $slug], ['name' => $name, 'status' => 'active', 'plan_id' => $plan->id]);
            User::firstOrCreate(['email' => $email], ['business_id' => $business->id, 'name' => $name.' Owner', 'role' => 'owner', 'password' => 'password']);
            foreach ($domains as $domain) {
                $business->domains()->firstOrCreate(['domain' => $domain]);
            }
        }
    }
}
