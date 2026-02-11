<?php

namespace Tests\Unit\Services;

use App\Adapters\LibreTranslateAPI;
use App\Models\SavedWord;
use App\Models\User;
use App\Services\WordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class WordServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_returns_user_saved_words(): void
    {
        $user = User::factory()->create();
        SavedWord::factory()->count(3)->create(['user_id' => $user->id]);

        $request = Request::create('/api/words', 'GET');
        $request->setUserResolver(fn () => $user);

        $mock = $this->createMock(LibreTranslateAPI::class);
        $service = new WordService($mock);

        $result = $service->filter($request);

        $this->assertCount(3, $result);
    }

    public function test_filter_does_not_return_other_users_words(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        SavedWord::factory()->count(2)->create(['user_id' => $user->id]);
        SavedWord::factory()->count(3)->create(['user_id' => $otherUser->id]);

        $request = Request::create('/api/words', 'GET');
        $request->setUserResolver(fn () => $user);

        $mock = $this->createMock(LibreTranslateAPI::class);
        $service = new WordService($mock);

        $result = $service->filter($request);

        $this->assertCount(2, $result);
    }

    public function test_filter_eager_loads_lesson_relation(): void
    {
        $user = User::factory()->create();
        SavedWord::factory()->create(['user_id' => $user->id]);

        $request = Request::create('/api/words', 'GET');
        $request->setUserResolver(fn () => $user);

        $mock = $this->createMock(LibreTranslateAPI::class);
        $service = new WordService($mock);

        $result = $service->filter($request);

        $this->assertTrue($result->first()->relationLoaded('lesson'));
    }

    public function test_filter_by_search(): void
    {
        $user = User::factory()->create();
        SavedWord::factory()->create(['user_id' => $user->id, 'word' => 'beautiful']);
        SavedWord::factory()->create(['user_id' => $user->id, 'word' => 'morning']);

        $request = Request::create('/api/words', 'GET', ['search' => 'beautiful']);
        $request->setUserResolver(fn () => $user);

        $mock = $this->createMock(LibreTranslateAPI::class);
        $service = new WordService($mock);

        $result = $service->filter($request);

        $this->assertCount(1, $result);
        $this->assertEquals('beautiful', $result->first()->word);
    }

    public function test_filter_by_partial_search(): void
    {
        $user = User::factory()->create();
        SavedWord::factory()->create(['user_id' => $user->id, 'word' => 'beautiful']);
        SavedWord::factory()->create(['user_id' => $user->id, 'word' => 'beauty']);
        SavedWord::factory()->create(['user_id' => $user->id, 'word' => 'morning']);

        $request = Request::create('/api/words', 'GET', ['search' => 'beaut']);
        $request->setUserResolver(fn () => $user);

        $mock = $this->createMock(LibreTranslateAPI::class);
        $service = new WordService($mock);

        $result = $service->filter($request);

        $this->assertCount(2, $result);
    }

    public function test_filter_returns_all_when_no_search(): void
    {
        $user = User::factory()->create();
        SavedWord::factory()->count(4)->create(['user_id' => $user->id]);

        $request = Request::create('/api/words', 'GET');
        $request->setUserResolver(fn () => $user);

        $mock = $this->createMock(LibreTranslateAPI::class);
        $service = new WordService($mock);

        $result = $service->filter($request);

        $this->assertCount(4, $result);
    }

    public function test_filter_returns_empty_collection_when_user_has_no_words(): void
    {
        $user = User::factory()->create();

        $request = Request::create('/api/words', 'GET');
        $request->setUserResolver(fn () => $user);

        $mock = $this->createMock(LibreTranslateAPI::class);
        $service = new WordService($mock);

        $result = $service->filter($request);

        $this->assertCount(0, $result);
    }

    public function test_translate_delegates_to_translate_api(): void
    {
        $mock = $this->createMock(LibreTranslateAPI::class);
        $mock->expects($this->once())
            ->method('translate')
            ->with('hello')
            ->willReturn('olá');

        $service = new WordService($mock);

        $result = $service->translate('hello');

        $this->assertEquals('olá', $result);
    }

    public function test_translate_returns_api_response(): void
    {
        $mock = $this->createMock(LibreTranslateAPI::class);
        $mock->method('translate')
            ->with('good morning')
            ->willReturn('bom dia');

        $service = new WordService($mock);

        $result = $service->translate('good morning');

        $this->assertEquals('bom dia', $result);
    }
}
