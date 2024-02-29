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

        if (! app()->isProduction()) {
            \App\Models\User::factory()->create([
                'name' => 'Benny Rahmat',
                'email' => 'akunbeben@gmail.com',
                'paid' => false,
            ]);

            \App\Models\User::factory()->create([
                'name' => 'Demo Account',
                'email' => 'demo.pesenin.online@gmail.com',
                'paid' => false,
            ]);

            $this->call([DevelopmentSeeder::class]);
        }
    }
}
