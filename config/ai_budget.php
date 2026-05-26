<?php

return [
    'notice' => [
        'version' => env('AI_BUDGET_NOTICE_VERSION', '1.00'),
        'title' => env('AI_BUDGET_NOTICE_TITLE', 'AI Budget Beta Notice'),
        'description' => env('AI_BUDGET_NOTICE_DESCRIPTION', 'You are using AI Budget version 1.00 (beta). This feature currently estimates budget using guest count and budget range only.'),
        'points' => [
            'This assistant currently estimates based on your guest count and selected budget range.',
            'The chat output is displayed for this session and is not remembered as long-term memory.',
            'Responses may improve in upcoming versions as we expand the model and prompts.',
        ],
        'button_label' => env('AI_BUDGET_NOTICE_BUTTON_LABEL', 'I Understand'),
    ],
    'actions' => [
        'print_label' => env('AI_BUDGET_PRINT_LABEL', 'Print PDF'),
        'forget_label' => env('AI_BUDGET_FORGET_LABEL', 'Forget Output'),
        'forget_message' => env('AI_BUDGET_FORGET_MESSAGE', 'The AI output has been cleared from this screen.'),
    ],
];
