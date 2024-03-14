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

        \App\Models\User::query()->create([
            'name' => 'Benny Rahmat',
            'email' => 'akunbeben@gmail.com',
            'password' => bcrypt(env('APP_CENTRAL_PASS')),
            'paid' => false,
        ]);

        if (! app()->isProduction()) {
            \App\Models\User::factory()->create([
                'name' => 'Demo Account',
                'email' => 'demo.pesenin.online@gmail.com',
                'paid' => false,
            ]);

            $this->call([DevelopmentSeeder::class]);
        }
    }
}
