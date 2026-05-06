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
        return $this->callGeminiAPI($prompt);
    }

    /**
     * Send a chat message and get AI response
     */
    public function chatMessage(string $message, Couple $couple, int $guestCount, string $budgetRange): string
    {
        $conversationContext = $this->buildConversationContext($couple, $guestCount, $budgetRange);
        $fullPrompt = $conversationContext . "\n\nUser Message: " . $message;

        return $this->callGeminiAPI($fullPrompt);
    }

    /**
     * Build initial budget estimation prompt
     */
    private function buildBudgetPrompt(Couple $couple, int $guestCount, string $budgetRange): string
    {
        return "You are a professional wedding planner and budget advisor. Based on the following information, provide a detailed and helpful wedding budget estimation.\n\n" .
               "Guest Count: {$guestCount} people\n" .
               "Budget Range: {$budgetRange}\n\n" .
               "Please provide:\n" .
               "1. A recommended total budget for the wedding\n" .
               "2. Breakdown of typical expense categories (venue, catering, decoration, photography, etc.)\n" .
               "3. Suggested allocation percentage for each category\n" .
               "4. Money-saving tips specific to their guest count and budget\n" .
               "5. Any recommendations based on the budget range they selected\n\n" .
               "Format your response in a clear, organized manner that's easy to understand and implement.";
    }

    /**
     * Build conversation context for follow-up messages
     */
    private function buildConversationContext(Couple $couple, int $guestCount, string $budgetRange): string
    {
        return "You are a professional wedding planner and budget advisor helping a couple plan their wedding.\n" .
               "Guest Count: {$guestCount} people\n" .
               "Budget Range: {$budgetRange}\n\n" .
               "Provide helpful, practical advice for wedding planning and budgeting. Keep responses concise and actionable.";
    }

    /**
     * Call Gemini API and get response
     */
    private function callGeminiAPI(string $prompt): string
    {
        try {
            $response = Gemini::generativeModel(model: self::GEMINI_MODEL)->generateContent($prompt);

            return $response->text();
        } catch (\Exception $e) {
            Log::error('Gemini API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return '';
        }
    }
}