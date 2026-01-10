<?php

namespace Database\Seeders;

use Database\Seeders\seeders\CategorySeeder;
use Database\Seeders\seeders\OrganizationSeeder;
use Database\Seeders\seeders\UserSeeder;
use Database\Seeders\seeders\VariationSeeder;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
