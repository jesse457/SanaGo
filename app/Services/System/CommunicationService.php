<?php

namespace App\Services\System;

use App\Enums\FeedbackStatus;
use App\Models\Feedback;

class CommunicationService
{
    public function submitFeedback(array $data): Feedback
    {
        return Feedback::create([
            ...$data,
            'user_id' => auth()->id(),
            'status' => FeedbackStatus::PENDING->value,
        ]);
    }

    public function respondToFeedback(Feedback $feedback, string $response): Feedback
    {
        $feedback->update([
            'response' => $response,
            'responder_id' => auth()->id(),
            'status' => FeedbackStatus::ADDRESSED->value,
        ]);

        return $feedback;
    }
}
