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
        \App\Models\User::factory()->create([
            'name' => 'Benny Rahmat',
            'email' => 'akunbeben@gmail.com',
        ]);

        $this->call([DevelopmentSeeder::class]);
    }
}
