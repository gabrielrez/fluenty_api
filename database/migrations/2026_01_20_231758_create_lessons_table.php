<?php

use App\Enums\LessonLevelEnum;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->longText('text');
            $table->longText('translation');
            $table->string('image_url')->nullable();
            $table->string('audio_url');
            $table->unsignedInteger('duration');
            $table->string('level')->default(LessonLevelEnum::Beginner->value);
            $table->foreignIdFor(Category::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
