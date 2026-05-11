<?php

namespace App\Services;

use App\Models\Couple;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class AiBudgetService
{
    private const GEMINI_MODEL = 'gemini-2.0-flash';

    /**
     * Generate budget estimation based on guest count and preferences
     */
    public function estimateBudget(Couple $couple, int $guestCount, string $budgetRange): string
    {
        $prompt = $this->buildBudgetPrompt($couple, $guestCount, $budgetRange);
        $response = $this->callGeminiAPI($prompt);

        if ($response === '' || $response === 'RATE_LIMIT_EXCEEDED') {
            return $this->buildFallbackEstimate($guestCount, $budgetRange);
        }

        return $response;
    }

    /**
     * Send a chat message and get AI response
     */
    public function chatMessage(string $message, Couple $couple, int $guestCount, string $budgetRange): string
    {
        $conversationContext = $this->buildConversationContext($couple, $guestCount, $budgetRange);
        $fullPrompt = $conversationContext."\n\nUser Message: ".$message;
        $response = $this->callGeminiAPI($fullPrompt);

        if ($response === '' || $response === 'RATE_LIMIT_EXCEEDED') {
            return $this->buildFallbackChatResponse($guestCount, $budgetRange);
        }

        return $response;
    }

    /**
     * Build initial budget estimation prompt
     */
    private function buildBudgetPrompt(Couple $couple, int $guestCount, string $budgetRange): string
    {
        return "Wedding budget for {$guestCount} guests, {$budgetRange}:\n".
               "1. Total budget estimate\n".
               "2. Main expenses: venue, catering, decor, photo, music\n".
               "3. Budget % for each\n".
               "4. Cost-saving tips\n".
               'Keep it concise.';
    }

    /**
     * Build conversation context for follow-up messages
     */
    private function buildConversationContext(Couple $couple, int $guestCount, string $budgetRange): string
    {
        return "Wedding budget advisor. {$guestCount} guests, {$budgetRange}. Brief, practical answers.";
    }

    /**
     * Build a local fallback estimate when the AI API is unavailable.
     */
    private function buildFallbackEstimate(int $guestCount, string $budgetRange): string
    {
        $estimatedTotal = $this->estimateTotalBudget($guestCount, $budgetRange);
        $breakdown = $this->buildBudgetBreakdown($estimatedTotal);

        return implode("\n", [
            'Estimated wedding budget',
            "Guest count: {$guestCount}",
            "Budget range: {$budgetRange}",
            'Recommended total budget: RM '.number_format($estimatedTotal),
            'Suggested breakdown:',
            '1. Venue: RM '.number_format($breakdown['venue']),
            '2. Catering: RM '.number_format($breakdown['catering']),
            '3. Decor: RM '.number_format($breakdown['decor']),
            '4. Photo and video: RM '.number_format($breakdown['photo_video']),
            '5. Music and emcee: RM '.number_format($breakdown['music_emcee']),
            '6. Contingency: RM '.number_format($breakdown['contingency']),
            'Tip: Keep a small buffer for last-minute costs and extra guests.',
        ]);
    }

    /**
     * Build a short fallback chat response when the AI API is unavailable.
     */
    private function buildFallbackChatResponse(int $guestCount, string $budgetRange): string
    {
        $estimatedTotal = $this->estimateTotalBudget($guestCount, $budgetRange);

        return "Based on {$guestCount} guests and {$budgetRange}, a practical estimate is RM ".number_format($estimatedTotal).".\n"
            .'If you want, I can also break this down by venue, catering, decor, and contingency.';
    }

    /**
     * Estimate a realistic total wedding budget from the selected range and guest count.
     */
    private function estimateTotalBudget(int $guestCount, string $budgetRange): int
    {
        $normalizedBudgetRange = $this->normalizeBudgetRange($budgetRange);

        return match ($normalizedBudgetRange) {
            'RM 10000 - RM 20000' => $this->estimateWithinBounds($guestCount * 140, 10000, 20000),
            'RM 25000 - RM 40000' => $this->estimateWithinBounds($guestCount * 220, 25000, 40000),
            'RM 50000 And Above' => max(50000, $guestCount * 250),
            default => max(10000, $guestCount * 160),
        };
    }

    /**
     * Build a simple percentage breakdown for the total budget.
     *
     * @return array{venue:int,catering:int,decor:int,photo_video:int,music_emcee:int,contingency:int}
     */
    private function buildBudgetBreakdown(int $totalBudget): array
    {
        $venue = (int) round($totalBudget * 0.3);
        $catering = (int) round($totalBudget * 0.35);
        $decor = (int) round($totalBudget * 0.1);
        $photoVideo = (int) round($totalBudget * 0.1);
        $musicEmcee = (int) round($totalBudget * 0.05);
        $contingency = max(0, $totalBudget - ($venue + $catering + $decor + $photoVideo + $musicEmcee));

        return [
            'venue' => $venue,
            'catering' => $catering,
            'decor' => $decor,
            'photo_video' => $photoVideo,
            'music_emcee' => $musicEmcee,
            'contingency' => $contingency,
        ];
    }

    private function estimateWithinBounds(int $estimatedAmount, int $minimumAmount, int $maximumAmount): int
    {
        return min($maximumAmount, max($minimumAmount, $estimatedAmount));
    }

    private function normalizeBudgetRange(string $budgetRange): string
    {
        return match ($budgetRange) {
            'RM 2500 - RM 40000' => 'RM 25000 - RM 40000',
            default => $budgetRange,
        };
    }

    /**
     * Call Gemini API and get response
     */
    private function callGeminiAPI(string $prompt): string
    {
        try {
            $response = Gemini::generativeModel(model: self::GEMINI_MODEL)->generateContent($prompt);

            return $response->text();
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $isTooManyRequests = str_contains($message, '429') || str_contains($message, 'TooManyRequests') || str_contains($message, 'quota');

            Log::error('Gemini API Error', [
                'message' => $message,
                'code' => $e->getCode(),
                'is_rate_limit' => $isTooManyRequests,
            ]);

            if ($isTooManyRequests) {
                return 'RATE_LIMIT_EXCEEDED';
            }

            return '';
        }
    }
}
