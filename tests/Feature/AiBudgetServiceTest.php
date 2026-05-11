<?php

use App\Models\Couple;
use App\Services\AiBudgetService;
use Gemini\Laravel\Facades\Gemini;

it('returns a fallback budget estimate when gemini is rate limited', function () {
    Gemini::fake();

    $service = app(AiBudgetService::class);

    $response = $service->estimateBudget(new Couple, 150, 'RM 25000 - RM 40000');

    expect($response)
        ->not->toBeEmpty()
        ->toContain('Estimated wedding budget')
        ->toContain('Recommended total budget: RM');
});
