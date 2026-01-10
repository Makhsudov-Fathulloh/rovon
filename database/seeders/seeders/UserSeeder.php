<?php

namespace Database\Seeders\seeders;

use App\Models\Role;
use App\Services\StatusService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // roles
        Role::insert([
            ['title' => 'Developer', 'description' => 'Developer - барча ҳуқуқларга эга дастурчи.'],
            ['title' => 'Admin', 'description' => 'Admin - барча ҳуқуқларга эга асосий фойдаланувчи.'],
            ['title' => 'Manager', 'description' => 'Manager - admin томонидан ҳуқуқлари чекланадиган иккинчи фойдаланувчи.'],
            ['title' => 'Moderator', 'description' => 'Moderator - admin томонидан ҳуқуқлари чекланадиган учинчи фойдаланувчи.'],
            ['title' => 'Master', 'description' => 'Master - тизимдан фойдаланувчи оддий ишчи, ҳунинг ҳуқуқлари чекланган.'],
            ['title' => 'Worker', 'description' => 'Worker - тизимдан фойдаланувчи оддий ишчи, ҳунинг ҳуқуқлари чекланган.'],
            ['title' => 'Client', 'description' => 'Client - тизимдан фойдаланувчи оддий мижоз, ҳунинг ҳуқуқлари чекланган.'],
        ]);

        $roleMap = Role::pluck('id', 'title')->toArray();

        // user
        DB::table('user')->insert([
            'first_name' => 'Developer',
            'last_name' => 'Developer',
            'username' => 'Developer',
            'password_hash' => Hash::make('castle4525'),
            'email' => 'developer@gmail.com',
            'email_verified_at' => now(),
            'photo' => null,
            'phone' => '+998944344525',
            'telegram_chat_id' => 994411739,
            'role_id' => $roleMap['Developer'],
            'status' => StatusService::STATUS_ACTIVE,
            'remember_token' => Str::random(10),
            'token' => Str::random(32),
            'auth_key' => Str::random(32),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin
        DB::table('user')->insert([
            'first_name' => 'Azizbek',
            'last_name' => 'Azizbek',
            'username' => 'Azizbek',
            'password_hash' => Hash::make('castle4525'),
            'email' => 'azizbek@gmail.com',
            'email_verified_at' => now(),
            'photo' => null,
            'phone' => '+998906259777',
            'telegram_chat_id' => 267171275,
            'role_id' => $roleMap['Admin'],
            'status' => StatusService::STATUS_ACTIVE,
            'remember_token' => Str::random(10),
            'token' => Str::random(32),
            'auth_key' => Str::random(32),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
         DB::table('user')->insert([
            'first_name' => 'Imronbek',
            'last_name' => 'Imronbek',
            'username' => 'Imronbek',
            'password_hash' => Hash::make('castle4525'),
            'email' => 'imronbek@gmail.com',
            'email_verified_at' => now(),
            'photo' => null,
            'phone' => '+998906259777',
            'telegram_chat_id' => 7477878420,
            'role_id' => $roleMap['Admin'],
            'status' => StatusService::STATUS_ACTIVE,
            'remember_token' => Str::random(10),
            'token' => Str::random(32),
            'auth_key' => Str::random(32),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Moderator
        DB::table('user')->insert([
            'first_name' => 'Avazbek',
            'last_name' => 'Avazbek',
            'username' => 'Avazbek',
            'password_hash' => Hash::make('castle4525'),
            'email' => 'avazbek@gmail.com',
            'email_verified_at' => now(),
            'photo' => null,
            'phone' => '+998000000002',
            'role_id' => $roleMap['Moderator'],
            'status' => StatusService::STATUS_ACTIVE,
            'remember_token' => Str::random(10),
            'token' => Str::random(32),
            'auth_key' => Str::random(32),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Client
        DB::table('user')->insert([
            'first_name' => 'Стандарт',
            'last_name' => 'Клиент',
            'username' => 'Стандарт клиент',
            'password_hash' => Hash::make('castle4525'),
            'email' => 'standartclient@gmail.com',
            'email_verified_at' => now(),
            'photo' => null,
            'phone' => '+998000000040',
            'role_id' => $roleMap['Client'],
            'status' => StatusService::STATUS_ACTIVE,
            'remember_token' => Str::random(10),
            'token' => Str::random(32),
            'auth_key' => Str::random(32),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
