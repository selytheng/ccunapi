<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // Create Feedback
    public function createFeedback(Request $request)
    {
        $request->validate([
            'partner_id' => 'nullable|exists:partners,id',
            'name' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        $feedback = Feedback::create($request->all());

        return response()->json([
            'message' => 'Feedback submitted successfully!',
            'data' => $feedback,
        ], 201);
    }

    // Get Feedback by Partner ID
    public function getFeedbackByPartnerId($partner_id)
    {
        $feedbacks = Feedback::where('partner_id', $partner_id)->get();

        return response()->json([
            'message' => 'Feedback retrieved successfully!',
            'data' => $feedbacks,
        ], 200);
    }

    // Get Feedback by Feedback ID
    public function getFeedbackById($id)
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json([
                'message' => 'Feedback not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Feedback retrieved successfully!',
            'data' => $feedback,
        ], 200);
    }
}
