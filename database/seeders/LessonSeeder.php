<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Language;
use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $languages = Language::all();
        $categories = Category::all();

        if ($languages->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $english_lessons = [
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

        $spanish_lessons = [
            [
                'title' => 'Buenos Días',
                'text' => '¡Buenos días! ¿Cómo estás hoy? Espero que tengas un día maravilloso.',
                'translation' => 'Bom dia! Como você está hoje? Espero que tenha um dia maravilhoso.',
                'audio_url' => 'https://example.com/audio/buenos-dias-es.mp3',
                'duration' => 120,
                'category' => 'viagem',
            ],
            [
                'title' => 'Clima',
                'text' => 'Es un hermoso día soleado. El clima es perfecto para actividades al aire libre.',
                'translation' => 'É um lindo dia ensolarado. O clima é perfeito para atividades ao ar livre.',
                'audio_url' => 'https://example.com/audio/clima-es.mp3',
                'duration' => 180,
                'category' => 'cotidiano',
            ],
            [
                'title' => 'En el Restaurante',
                'text' => 'Perdón, ¿podría tener una mesa para dos? Nos gustaría pedir agua y aperitivos.',
                'translation' => 'Com licença, poderia ter uma mesa para dois? Gostaríamos de pedir água e entradas.',
                'audio_url' => 'https://example.com/audio/restaurante-es.mp3',
                'duration' => 240,
                'category' => 'cotidiano',
            ],
            [
                'title' => 'En el Aeropuerto',
                'text' => '¿Dónde está la puerta de salida? Necesito revisar mi equipaje y pasar por seguridad.',
                'translation' => 'Onde é o portão de partida? Preciso verificar minha bagagem e passar pela segurança.',
                'audio_url' => 'https://example.com/audio/aeropuerto-es.mp3',
                'duration' => 200,
                'category' => 'viagem',
            ],
        ];

        $french_lessons = [
            [
                'title' => 'Bonjour',
                'text' => 'Bonjour! Comment allez-vous? J\'espère que vous avez une belle journée.',
                'translation' => 'Olá! Como você está? Espero que tenha um lindo dia.',
                'audio_url' => 'https://example.com/audio/bonjour-fr.mp3',
                'duration' => 120,
                'category' => 'viagem',
            ],
            [
                'title' => 'Au Musée',
                'text' => 'Bienvenue au musée. Les peintures sont magnifiques et les sculptures sont impressionnantes.',
                'translation' => 'Bem-vindo ao museu. As pinturas são magníficas e as esculturas são impressionantes.',
                'audio_url' => 'https://example.com/audio/musee-fr.mp3',
                'duration' => 200,
                'category' => 'arte & cultura',
            ],
            [
                'title' => 'Au Restaurant',
                'text' => 'Excusez-moi, avez-vous une table pour deux? Je voudrais commander de l\'eau et des entrées.',
                'translation' => 'Com licença, você tem uma mesa para dois? Gostaria de pedir água e entradas.',
                'audio_url' => 'https://example.com/audio/restaurant-fr.mp3',
                'duration' => 240,
                'category' => 'cotidiano',
            ],
        ];

        $english_language = $languages->where('code', 'en')->first();
        $spanish_language = $languages->where('code', 'es')->first();
        $french_language = $languages->where('code', 'fr')->first();

        if ($english_language) {
            foreach ($english_lessons as $lesson_data) {
                $category_name = $lesson_data['category'];
                $category = $categories->where('name', $category_name)->first();
                unset($lesson_data['category']);

                Lesson::create([
                    ...$lesson_data,
                    'language_id' => $english_language->id,
                    'category_id' => $category?->id,
                ]);
            }
        }

        if ($spanish_language) {
            foreach ($spanish_lessons as $lesson_data) {
                $category_name = $lesson_data['category'];
                $category = $categories->where('name', $category_name)->first();
                unset($lesson_data['category']);

                Lesson::create([
                    ...$lesson_data,
                    'language_id' => $spanish_language->id,
                    'category_id' => $category?->id,
                ]);
            }
        }

        if ($french_language) {
            foreach ($french_lessons as $lesson_data) {
                $category_name = $lesson_data['category'];
                $category = $categories->where('name', $category_name)->first();
                unset($lesson_data['category']);

                Lesson::create([
                    ...$lesson_data,
                    'language_id' => $french_language->id,
                    'category_id' => $category?->id,
                ]);
            }
        }
    }
}
