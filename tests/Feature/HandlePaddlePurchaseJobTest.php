<?php


use App\Jobs\HandlePaddlePurchaseJob;
use App\Mail\NewPurchasedMail;
use App\Models\PurchasedCourse;
use App\Models\User;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('stores paddle purchase', function () {
    // Assert
    $this->assertDatabaseEmpty(User::class);
    $this->assertDatabaseEmpty(PurchasedCourse::class);

    // Arrange
    $course = \App\Models\Course::factory()->create([
        'paddle_product_id' => 'pro_01j449j1rwpm6e7y7ts4mp2wn4',
    ]);
    $webhookCall = \Spatie\WebhookClient\Models\WebhookCall::create([
        'name' => 'default',
        'url' => 'some-url',
        'payload' => [
            'email' => 'test@test.es',
            'name' => 'Test User',
            'p_product_id' => 'pro_01j449j1rwpm6e7y7ts4mp2wn4',
        ]
    ]);

    // Act
    (new HandlePaddlePurchaseJob($webhookCall))->handle();

    // Assert
    assertDatabaseHas(User::class, [
        'email' => 'test@test.es',
        'name' => 'Test User',
    ]);
    $user = User::where('email', 'test@test.es')->first();
    assertDatabaseHas(PurchasedCourse::class, [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
});

it('stores paddle purchase for given user', function () {
    // Arrange
    $user = User::factory()->create([
        'email' => 'test@test.es',
    ]);
    $course = \App\Models\Course::factory()->create([
        'paddle_product_id' => 'pro_01j449j1rwpm6e7y7ts4mp2wn4',
    ]);
    $webhookCall = \Spatie\WebhookClient\Models\WebhookCall::create([
        'name' => 'default',
        'url' => 'some-url',
        'payload' => [
            'email' => 'test@test.es',
            'name' => 'Test User',
            'p_product_id' => 'pro_01j449j1rwpm6e7y7ts4mp2wn4',
        ]
    ]);

    // Act
    (new HandlePaddlePurchaseJob($webhookCall))->handle();

    // Assert
    assertDatabaseCount(User::class, 1);
    assertDatabaseHas(User::class, [
        'email' => $user->email,
        'name' => $user->name,
    ]);
    assertDatabaseHas(PurchasedCourse::class, [
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);
});

it('sends a user email', function () {
    // Arrange
    Mail::fake();
    $course = \App\Models\Course::factory()->create([
        'paddle_product_id' => 'pro_01j449j1rwpm6e7y7ts4mp2wn4',
    ]);
    $webhookCall = \Spatie\WebhookClient\Models\WebhookCall::create([
        'name' => 'default',
        'url' => 'some-url',
        'payload' => [
            'email' => 'test@test.es',
            'name' => 'Test User',
            'p_product_id' => 'pro_01j449j1rwpm6e7y7ts4mp2wn4',
        ]
    ]);

    // Act
    (new HandlePaddlePurchaseJob($webhookCall))->handle();

    // Assert
    Mail::assertSent(NewPurchasedMail::class);
});
