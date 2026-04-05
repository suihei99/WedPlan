<?php

namespace App\Services;

use App\Models\Couple;

class AiBudgetService
{
    /**
     * consultAIChatBot() on Couple class
     * method to generate a budget plan based on user input and preferences.
     * This method will utilize AI algorithms to analyze the user's financial data and provide personalized budget recommendations.
     */

    public function estimateBudget(Couple $couple): array
    {
      $prompt = $this->buildPrompt($couple);

      $response = $this->callAIChatBot($prompt);

      return $response;

    }

    public function buildPrompt(Couple $couple): string
    {
        // Build a prompt based on the couple's financial data and preferences
        $prompt = "Based on the following financial data and preferences, provide a personalized budget plan:\n";
        $prompt .= "Income: " . $couple->income . "\n";
        $prompt .= "Expenses: " . $couple->expenses . "\n";
        $prompt .= "Savings Goals: " . $couple->savings_goals . "\n";
        // Add more relevant data as needed

        return $prompt;
    }
}