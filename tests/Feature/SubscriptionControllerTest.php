<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createSubscribedUser(): User
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_test_123',
        ]);

        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test_123',
        ]);

        return $user;
    }

    public function test_plans_returns_available_plans(): void
    {
        $user = User::factory()->create();

        config([
            'services.stripe.price_monthly' => 'price_monthly_test',
            'services.stripe.price_yearly' => 'price_yearly_test',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/subscription/plans');

        $response->assertOk()
            ->assertJson([
                'plans' => [
                    [
                        'name' => 'Mensal',
                        'price_id' => 'price_monthly_test',
                        'amount' => 3790,
                        'interval' => 'month',
                    ],
                    [
                        'name' => 'Anual',
                        'price_id' => 'price_yearly_test',
                        'amount' => 39790,
                        'interval' => 'year',
                    ],
                ],
            ]);
    }

    public function test_plans_requires_authentication(): void
    {
        $response = $this->getJson('/api/subscription/plans');

        $response->assertStatus(401);
    }

    public function test_lesson_show_requires_subscription(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'subscription_required',
                'message' => 'An active subscription is required to access this resource.',
            ]);
    }

    public function test_lesson_start_requires_subscription(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/start");

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'subscription_required',
            ]);
    }

    public function test_lesson_toggle_complete_requires_subscription(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/toggle-complete");

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'subscription_required',
            ]);
    }

    public function test_lesson_index_does_not_require_subscription(): void
    {
        $user = User::factory()->create();
        Lesson::factory()->count(2)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/lessons');

        $response->assertOk();
    }

    public function test_subscribed_user_can_access_lesson_show(): void
    {
        $user = $this->createSubscribedUser();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/lessons/{$lesson->id}");

        $response->assertOk();
    }

    public function test_subscribed_user_can_start_lesson(): void
    {
        $user = $this->createSubscribedUser();
        $lesson = Lesson::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/lessons/{$lesson->id}/start");

        $response->assertOk();
    }

    public function test_checkout_blocks_already_subscribed_user(): void
    {
        $user = $this->createSubscribedUser();

        config(['services.stripe.price_monthly' => 'price_monthly_test']);

        $response = $this->actingAs($user)
            ->postJson('/api/subscription/checkout', [
                'price_id' => 'price_monthly_test',
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'User already has an active subscription. Use the billing portal to manage your plan.',
            ]);
    }

    public function test_checkout_requires_authentication(): void
    {
        $response = $this->postJson('/api/subscription/checkout', [
            'price_id' => 'price_test_123',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
        ]);

        $response->assertStatus(401);
    }

    public function test_checkout_rejects_invalid_price_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/subscription/checkout', [
                'price_id' => 'price_invalid_123',
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price_id']);
    }

    public function test_checkout_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/subscription/checkout', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price_id', 'success_url', 'cancel_url']);
    }

    public function test_status_returns_not_subscribed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/subscription/status');

        $response->assertOk()
            ->assertJson([
                'subscribed' => false,
            ]);
    }

    public function test_status_returns_subscribed_data(): void
    {
        $user = $this->createSubscribedUser();

        $response = $this->actingAs($user)
            ->getJson('/api/subscription/status');

        $response->assertOk()
            ->assertJson([
                'subscribed' => true,
                'stripe_status' => 'active',
            ]);
    }

    public function test_status_requires_authentication(): void
    {
        $response = $this->getJson('/api/subscription/status');

        $response->assertStatus(401);
    }

    public function test_billing_portal_requires_stripe_customer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/subscription/billing-portal');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'No billing history found. Subscribe to a plan first.',
            ]);
    }

    public function test_billing_portal_requires_authentication(): void
    {
        $response = $this->postJson('/api/subscription/billing-portal');

        $response->assertStatus(401);
    }
}
