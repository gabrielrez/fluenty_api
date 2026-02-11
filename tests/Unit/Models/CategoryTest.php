<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $category = new Category;

        $this->assertEquals(
            ['name', 'description'],
            $category->getFillable()
        );
    }

    public function test_has_many_lessons(): void
    {
        $category = Category::factory()->create();
        Lesson::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->lessons);
        $this->assertInstanceOf(Lesson::class, $category->lessons->first());
    }

    public function test_can_be_created_with_factory(): void
    {
        $category = Category::factory()->create();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    public function test_can_be_created_without_description(): void
    {
        $category = Category::factory()->create(['description' => null]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'description' => null,
        ]);
    }
}
