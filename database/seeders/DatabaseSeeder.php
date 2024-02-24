<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        if (!app()->isProduction()) {
            \App\Models\User::query()->create([
                'name' => 'Benny Rahmat',
                'email' => 'akunbeben@gmail.com',
            ]);

            \App\Models\User::factory()->create([
                'name' => 'Benny Rahmat',
                'email' => 'beben.devs@gmail.com',
                'paid' => true,
            ]);

            $this->call([DevelopmentSeeder::class]);
        }
    }
}
