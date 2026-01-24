<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return;
        }

        $lessons = [
            [
                'title' => 'Good Morning',
                'text' => 'Good morning! How are you today? I hope you have a wonderful day ahead.',
                'translation' => 'Bom dia! Como você está hoje? Espero que tenha um dia maravilhoso pela frente.',
                'audio_url' => 'https://example.com/audio/good-morning-en.mp3',
                'duration' => 120,
                'category' => 'viagem',
            ],
            [
                'title' => 'Weather',
                'text' => 'It is a beautiful sunny day today. The weather is perfect for outdoor activities.',
                'translation' => 'É um lindo dia ensolarado hoje. O clima é perfeito para atividades ao ar livre.',
                'audio_url' => 'https://example.com/audio/weather-en.mp3',
                'duration' => 180,
                'category' => 'cotidiano',
            ],
            [
                'title' => 'At the Restaurant',
                'text' => 'Excuse me, could I have a table for two? We would like to order some water and appetizers.',
                'translation' => 'Com licença, poderia ter uma mesa para dois? Gostaríamos de pedir água e entradas.',
                'audio_url' => 'https://example.com/audio/restaurant-en.mp3',
                'duration' => 240,
                'category' => 'cotidiano',
            ],
            [
                'title' => 'At the Airport',
                'text' => 'Where is the departure gate? I need to check my luggage and go through security.',
                'translation' => 'Onde é o portão de partida? Preciso verificar minha bagagem e passar pela segurança.',
                'audio_url' => 'https://example.com/audio/airport-en.mp3',
                'duration' => 200,
                'category' => 'viagem',
            ],
            [
                'title' => 'Shopping at the Store',
                'text' => 'How much does this shirt cost? Do you have it in a different size?',
                'translation' => 'Quanto custa esta camisa? Você a tem em um tamanho diferente?',
                'audio_url' => 'https://example.com/audio/shopping-en.mp3',
                'duration' => 150,
                'category' => 'cotidiano',
            ],
            [
                'title' => 'Business Meeting',
                'text' => 'Good morning everyone. Let\'s review the quarterly report and discuss our goals.',
                'translation' => 'Bom dia a todos. Vamos revisar o relatório trimestral e discutir nossos objetivos.',
                'audio_url' => 'https://example.com/audio/business-meeting-en.mp3',
                'duration' => 300,
                'category' => 'negócios & carreira',
            ],
        ];

        foreach ($lessons as $lesson_data) {
            $category_name = $lesson_data['category'];
            $category = $categories->where('name', $category_name)->first();
            unset($lesson_data['category']);

            Lesson::create([
                ...$lesson_data,
                'category_id' => $category?->id,
            ]);
        }
    }
}
