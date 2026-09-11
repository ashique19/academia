<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PromotionSeeder::class,
            BlogCategorySeeder::class,
            FaqSeeder::class,
            TestimonialSeeder::class,
        ]);

        if (app()->environment('local', 'testing')) {
            $this->seedStaff();
        }
    }

    private function seedStaff(): void
    {
        $accounts = [
            ['Super Admin',    'super@academiatraining.eu',   'super-admin'],
            ['Site Admin',     'admin@academiatraining.eu',   'admin'],
            ['Course Manager', 'courses@academiatraining.eu', 'course-manager'],
            ['Sales Manager',  'sales@academiatraining.eu',   'sales-manager'],
            ['Content Editor', 'content@academiatraining.eu', 'content-editor'],
        ];

        foreach ($accounts as [$name, $email, $role]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $name,
                    // Local development only — DatabaseSeeder::run() gates this
                    // block on the environment so it can never run in production.
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]
            );

            $user->syncRoles([$role]);
        }
    }
}
