<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\SavedWord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavedWord>
 */
class SavedWordFactory extends Factory
{
    protected $model = SavedWord::class;

    public function definition(): array
    {
        return [
            'word' => fake()->word(),
            'translation' => fake()->word(),
            'context' => fake()->sentence(),
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
        ];
    }
}
