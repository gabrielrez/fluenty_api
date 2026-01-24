<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Gabriel Rezende',
            'email' => 'gabrielrezcpessoa@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $this->call(LanguageSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(LessonSeeder::class);
    }
}
