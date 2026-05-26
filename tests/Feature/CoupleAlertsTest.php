<?php

use App\Mail\UserAlertMail;
use App\Models\BudgetCategory;
use App\Models\Couple;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\ExpenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Contract\Messaging;

use function Pest\Laravel\artisan;
use function Pest\Laravel\mock;

uses(RefreshDatabase::class);

it('sends email, web, and push notifications when a category and overall budget are over limit', function () {
    Mail::fake();
    mock(Messaging::class, function ($mock) {
        $mock->shouldReceive('send')->twice()->andReturn('message-id');
    });

    $user = User::factory()->create([
        'device_token' => 'device-token-123',
    ]);

    $couple = Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
        'total_budget_limit' => 120,
    ]);

    $category = BudgetCategory::query()->create([
        'user_id' => $user->id,
        'category_name' => 'Catering',
        'allocated_amount' => 100,
    ]);

    $service = app(ExpenseService::class);
    $service->create($category, [
        'expense_name' => 'Wedding lunch',
        'amount' => 150,
        'date_paid' => now()->toDateString(),
        'description' => 'Large catering deposit',
        'payment_method' => 'Cash',
    ]);

    expect(UserNotification::query()->where('user_id', $user->id)->count())->toBe(2);
    expect(UserNotification::query()->where('user_id', $user->id)->pluck('title')->all())->toEqualCanonicalizing([
        'Budget Category Over Limit: Catering',
        'Budget Limit Exceeded',
    ]);

    Mail::assertSentTimes(UserAlertMail::class, 2);
    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) {
        return $mail->title === 'Budget Category Over Limit: Catering';
    });
    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) {
        return $mail->title === 'Budget Limit Exceeded';
    });
});

it('sends email, web, and push notifications for overdue tasks', function () {
    Mail::fake();
    mock(Messaging::class, function ($mock) {
        $mock->shouldReceive('send')->once()->andReturn('message-id');
    });

    $user = User::factory()->create([
        'device_token' => 'device-token-456',
    ]);

    $couple = Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
        'total_budget_limit' => 1000,
    ]);

    Task::query()->create([
        'user_id' => $couple->user_id,
        'task_name' => 'Confirm florist',
        'description' => 'Call the florist',
        'deadline' => now()->subDay()->toDateString(),
        'is_completed' => false,
        'priority' => Task::PRIORITY_HIGH,
    ]);

    artisan('alerts:dispatch-couple-notifications')->assertExitCode(0);

    expect(UserNotification::query()->where('user_id', $user->id)->count())->toBe(1);
    expect(UserNotification::query()->where('user_id', $user->id)->value('title'))->toBe('Overdue Task Reminder');

    Mail::assertSentTimes(UserAlertMail::class, 1);
    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) {
        return $mail->title === 'Overdue Task Reminder';
    });
});
