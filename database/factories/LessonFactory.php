<?php

namespace Database\Factories;

use App\Enums\LessonLevelEnum;
use App\Models\Category;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'text' => fake()->paragraphs(2, true),
            'translation' => fake()->paragraphs(2, true),
            'image_url' => fake()->imageUrl(),
            'audio_url' => fake()->url(),
            'duration' => fake()->numberBetween(60, 600),
            'category_id' => Category::factory(),
            'level' => fake()->randomElement(LessonLevelEnum::cases()),
        ];
    }

    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => LessonLevelEnum::Beginner,
        ]);
    }

    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => LessonLevelEnum::Intermediate,
        ]);
    }

    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => LessonLevelEnum::Advanced,
        ]);
    }
}
