<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'viagem',
            'cotidiano',
            'negócios & carreira',
            'tecnologia',
            'educação & conhecimento geral',
            'arte & cultura',
            'histórias',
            'história & sociedade',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
            ]);
        }
    }
}
