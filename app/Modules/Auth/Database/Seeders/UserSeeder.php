<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\User;
use Modules\User\Models\UserProfile;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $users = [
                'admin' => [
                    'email' => 'admin@example.com',
                    'password' => 'password',
                    'profile' => [
                        'first_name' => 'Super',
                        'last_name'  => 'Admin',
                    ],
                ],
                'editor' => [
                    'email' => 'editor@example.com',
                    'password' => 'password',
                    'profile' => [
                        'first_name' => 'Content',
                        'last_name'  => 'Editor',
                    ],
                ],
                'user' => [
                    'email' => 'user@example.com',
                    'password' => 'password',
                    'profile' => [
                        'first_name' => 'Regular',
                        'last_name'  => 'User',
                    ],
                ],
            ];

            foreach ($users as $role => $data) {
                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'password' => Hash::make($data['password']),
                        'status'   => 'active',
                    ]
                );

                if (! $user->hasRole($role)) {
                    $user->assignRole($role);
                }

                UserProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    $data['profile']
                );
            }
        });
    }
}
