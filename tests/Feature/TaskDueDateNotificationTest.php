<?php

use App\Mail\UserAlertMail;
use App\Models\Couple;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Contract\Messaging;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\mock;
use function Pest\Laravel\put;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('sends email, web, and push notifications when a couple updates a task due date from the web controller', function () {
    Mail::fake();
    mock(Messaging::class, function ($mock) {
        $mock->shouldReceive('send')->once()->andReturn('message-id');
    });

    $user = User::factory()->create([
        'device_token' => 'task-device-web',
    ]);

    $couple = Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
        'total_budget_limit' => 1000,
    ]);

    $task = Task::query()->create([
        'user_id' => $user->id,
        'task_name' => 'Confirm venue',
        'description' => 'Call the venue manager',
        'deadline' => '2026-06-10',
        'is_completed' => false,
        'priority' => Task::PRIORITY_HIGH,
    ]);

    actingAs($user);

    put(route('couple.tasks.update', $task), [
        'task_name' => 'Confirm venue',
        'description' => 'Call the venue manager',
        'deadline' => '2026-06-20',
        'priority' => Task::PRIORITY_HIGH,
    ])
        ->assertRedirect(route('couple.tasks.index'));

    $task->refresh();

    expect($task->deadline?->format('Y-m-d'))->toBe('2026-06-20');
    expect(UserNotification::query()->where('user_id', $user->id)->value('title'))->toBe('Task Due Date Updated: Confirm venue');

    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) use ($user) {
        return $mail->hasTo($user->email)
            && $mail->title === 'Task Due Date Updated: Confirm venue';
    });
});

it('sends email, web, and push notifications when a couple updates a task due date from the api controller', function () {
    Mail::fake();
    mock(Messaging::class, function ($mock) {
        $mock->shouldReceive('send')->once()->andReturn('message-id');
    });

    $user = User::factory()->create([
        'device_token' => 'task-device-api',
    ]);

    $couple = Couple::query()->create([
        'user_id' => $user->id,
        'partner_1_name' => 'Alya',
        'partner_2_name' => 'Ben',
        'total_budget_limit' => 1000,
    ]);

    $task = Task::query()->create([
        'user_id' => $user->id,
        'task_name' => 'Book photographer',
        'description' => 'Choose the final package',
        'deadline' => '2026-07-01',
        'is_completed' => false,
        'priority' => Task::PRIORITY_MEDIUM,
    ]);

    Sanctum::actingAs($user);

    putJson('/api/v1/couple/tasks/'.$task->id, [
        'task_name' => 'Book photographer',
        'description' => 'Choose the final package',
        'deadline' => '2026-07-15',
        'priority' => Task::PRIORITY_MEDIUM,
    ])
        ->assertSuccessful()
        ->assertJsonPath('message', 'Task updated successfully.')
        ->assertJsonPath('data.deadline', '2026-07-15T00:00:00.000000Z');

    $task->refresh();

    expect($task->deadline?->format('Y-m-d'))->toBe('2026-07-15');
    expect(UserNotification::query()->where('user_id', $user->id)->value('title'))->toBe('Task Due Date Updated: Book photographer');

    Mail::assertSent(UserAlertMail::class, function (UserAlertMail $mail) use ($user) {
        return $mail->hasTo($user->email)
            && $mail->title === 'Task Due Date Updated: Book photographer';
    });
});
