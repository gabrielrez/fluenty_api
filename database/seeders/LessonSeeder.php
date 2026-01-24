<?php

namespace Database\Seeders;

use App\Enums\LessonLevelEnum;
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

        $levels = LessonLevelEnum::cases();

        for ($i = 1; $i <= 100; $i++) {
            $category = $categories->random();
            $level = $levels[array_rand($levels)];

            Lesson::create([
                'title' => "Lesson {$i}",
                'text' => "This is the lesson content number {$i}. It is designed to help you improve your skills.",
                'translation' => "Este é o conteúdo da lição número {$i}. Ele foi criado para ajudar você a melhorar suas habilidades.",
                'audio_url' => "https://example.com/audio/lesson-{$i}.mp3",
                'duration' => rand(60, 420),
                'category_id' => $category->id,
                'level' => $level->value,
            ]);
        }
    }
}
