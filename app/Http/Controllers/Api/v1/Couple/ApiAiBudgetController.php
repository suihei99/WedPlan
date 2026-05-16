<?php

namespace App\Http\Controllers\Api\v1\Couple;

use App\Http\Controllers\Controller;
use App\Services\AiBudgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiAiBudgetController extends Controller
{
    public function __construct(private readonly AiBudgetService $aiService) {}

    public function estimateInitial(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'guest_count' => 'required|integer|min:1|max:10000',
            'budget_range' => 'required|string|in:RM 10000 - RM 20000,RM 25000 - RM 40000,RM 2500 - RM 40000,RM 50000 And Above,None Of Above',
        ]);

        try {
            $couple = Auth::user()->couple;

            if (! $couple) {
                return response()->json(['error' => 'Couple not found'], 404);
            }

            $estimation = $this->aiService->estimateBudget(
                $couple,
                $validated['guest_count'],
                $validated['budget_range']
            );

            if ($estimation === 'RATE_LIMIT_EXCEEDED') {
                return response()->json([
                    'success' => false,
                    'error' => 'You have sent too many requests in a short period. Please wait a moment before trying again.',
                    'rate_limited' => true,
                ], 429);
            }

            if (trim($estimation) === '') {
                return response()->json([
                    'success' => false,
                    'error' => 'Chat is currently unavailable. The assistant is offline.',
                    'offline' => true,
                ], 503);
            }

            return response()->json([
                'success' => true,
                'message' => $estimation,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI Budget Estimation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => config('app.debug') ? $e->getMessage() : 'Failed to generate budget estimation. Please try again.',
            ], 500);
        }
    }

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'guest_count' => 'required|integer|min:1|max:10000',
            'budget_range' => 'required|string',
        ]);

        try {
            $couple = Auth::user()->couple;

            if (! $couple) {
                return response()->json(['error' => 'Couple not found'], 404);
            }

            $response = $this->aiService->chatMessage(
                $validated['message'],
                $couple,
                $validated['guest_count'],
                $validated['budget_range']
            );

            if (trim($response) === '') {
                return response()->json([
                    'success' => false,
                    'error' => 'Chat is currently unavailable. The assistant is offline.',
                    'offline' => true,
                ], 503);
            }

            return response()->json([
                'success' => true,
                'message' => $response,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI Chat Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => config('app.debug') ? $e->getMessage() : 'Failed to process your message. Please try again.',
            ], 500);
        }
    }
}
