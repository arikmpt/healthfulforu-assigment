<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Database\Seeders\RoleSeeder;
use Modules\Auth\Database\Seeders\UserSeeder;
use Modules\Content\Database\Seeders\TopicSeeder;
use Modules\Content\Database\Seeders\ContentSeeder;
use Modules\Subscription\Database\Seeders\SubscriptionPlanSeeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $this->call(RoleSeeder::class);
       $this->call(UserSeeder::class);
       $this->call(TopicSeeder::class);
       $this->call(ContentSeeder::class);
       $this->call(SubscriptionPlanSeeder::class);
    }
}
