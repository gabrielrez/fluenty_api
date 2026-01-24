<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::create([
            'name' => 'Ingês',
            'code' => 'en',
        ]);

        Language::create([
            'name' => 'Espanhol',
            'code' => 'es',
        ]);

        Language::create([
            'name' => 'Francês',
            'code' => 'fr',
        ]);
    }
}
